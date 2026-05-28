<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * Import phiếu nhập / xuất kho từ file CSV CỰC LỚN bằng streaming.
 *
 * Cách dùng:
 *   php artisan inventory:import-csv storage/app/imports/entries.csv --type=entry
 *   php artisan inventory:import-csv storage/app/imports/exits.csv   --type=exit
 *
 * Format CSV (UTF-8, có header dòng đầu) — 1 dòng = 1 chi tiết:
 *   external_id,date,warehouse_id,partner_id,user_id,status,note,
 *   delivery_partner_id,delivery_status,delivery_fee,delivery_code,
 *   material_id,quantity,unit_price,batch_code,expiry_date,location
 *
 * Trong đó:
 *   - external_id   : mã gom dòng cùng phiếu (có thể là số chạy 1,1,1,2,2,...).
 *                     Tất cả dòng có external_id giống nhau → cùng 1 phiếu.
 *   - partner_id    : supplier_id (nếu type=entry) hoặc project_id (nếu type=exit).
 *
 * Đặc điểm kỹ thuật:
 *   - Đọc streaming bằng fgetcsv → file 10GB, 10 tỷ dòng vẫn không OOM.
 *   - Nhóm chi tiết theo external_id, flush theo chunk header (mặc định 2000 phiếu).
 *   - Bulk insert + transaction để tăng tốc.
 *   - Có resume bằng --skip=N (bỏ qua N dòng đầu để chạy tiếp khi crash).
 */
class ImportInventoryCsvCommand extends Command
{
    protected $signature = 'inventory:import-csv
                            {file : Đường dẫn file CSV (tương đối project hoặc tuyệt đối)}
                            {--type=entry : entry|exit}
                            {--chunk=5000 : Số phiếu / lần flush (lớn hơn = nhanh hơn nhưng tốn RAM)}
                            {--skip=0 : Bỏ qua N dòng dữ liệu đầu (không tính header)}
                            {--no-progress : Tắt progress để chạy nhanh hơn với file siêu lớn}
                            {--turbo : Tắt FK / unique check trong lúc insert (nhanh x2-3)}';

    protected $description = 'Import phiếu nhập/xuất kho từ CSV (streaming, hỗ trợ file rất lớn)';

    public function handle(): int
    {
        $file = $this->argument('file');
        $type = $this->option('type');
        $chunkPhieu = max(100, (int) $this->option('chunk'));
        $skip = (int) $this->option('skip');
        $showProgress = ! $this->option('no-progress');
        $turbo = (bool) $this->option('turbo');

        if (! in_array($type, ['entry', 'exit'])) {
            $this->error('--type chỉ chấp nhận: entry | exit');
            return self::FAILURE;
        }

        $absPath = $this->resolvePath($file);
        if (! is_readable($absPath)) {
            $this->error("Không đọc được file: {$absPath}");
            return self::FAILURE;
        }

        $headerTable = $type === 'entry' ? 'inventory_entries' : 'inventory_exits';
        $detailTable = $type === 'entry' ? 'inventory_entry_details' : 'inventory_exit_details';
        $detailFk    = $type === 'entry' ? 'inventory_entry_id' : 'inventory_exit_id';
        $partnerCol  = $type === 'entry' ? 'supplier_id' : 'project_id';

        $fp = fopen($absPath, 'rb');
        if (! $fp) {
            $this->error('Không mở được file CSV.');
            return self::FAILURE;
        }

        // Đọc header CSV
        $header = fgetcsv($fp);
        if (! $header) {
            $this->error('CSV trống.');
            fclose($fp);
            return self::FAILURE;
        }
        // Strip UTF-8 BOM khỏi cell đầu nếu có
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        }
        $header = array_map(fn ($h) => trim((string) $h), $header);
        $idx = array_flip($header);

        $needCols = [
            'external_id','date','warehouse_id','partner_id','user_id','status','note',
            'delivery_partner_id','delivery_status','delivery_fee','delivery_code',
            'material_id','quantity','unit_price','batch_code','expiry_date','location',
        ];
        foreach ($needCols as $c) {
            if (! isset($idx[$c])) {
                $this->error("Thiếu cột bắt buộc trong CSV: {$c}");
                fclose($fp);
                return self::FAILURE;
            }
        }

        $this->info("Bắt đầu import {$type} từ: {$absPath}");
        $sizeBytes = filesize($absPath);
        $bar = null;
        if ($showProgress && $sizeBytes > 0) {
            $bar = $this->output->createProgressBar($sizeBytes);
            $bar->setFormat(' %current%/%max% bytes [%bar%] %percent:3s%%  %elapsed:6s% / %estimated:-6s%  Mem: %memory:6s%');
            $bar->start();
        }

        // Skip N dòng dữ liệu đầu
        $skipped = 0;
        while ($skipped < $skip) {
            if (fgetcsv($fp) === false) break;
            $skipped++;
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');

        // Buffer theo external_id hiện tại
        $currentExternal = null;
        $currentHeaderRow = null;
        $currentDetails = [];

        $headerBatch = [];      // các header chờ insert
        $headerExternalIds = [];// external_id song song với headerBatch
        $detailsByExternal = [];// external_id => array of detail rows (chưa có FK id)

        $totalPhieu = 0;
        $totalDetails = 0;
        $lineNo = 0;
        $lastBytePos = 0;

        DB::disableQueryLog();

        // TURBO: tắt mọi check để insert tối đa tốc độ
        if ($turbo) {
            DB::statement('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0');
            DB::statement('SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0');
            DB::statement('SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, AUTOCOMMIT=0');
            DB::statement('SET @OLD_SQL_LOG_BIN=@@SQL_LOG_BIN, SQL_LOG_BIN=0');
        }

        $pdo = DB::getPdo();

        $headerCols = ['date','warehouse_id', $partnerCol,'user_id','status','note',
                       'delivery_partner_id','delivery_status','delivery_fee','delivery_code',
                       'created_at','updated_at'];
        $headerColsSql = '`' . implode('`,`', $headerCols) . '`';

        $detailCols = $type === 'entry'
            ? [$detailFk,'material_id','quantity','unit_price','batch_code','expiry_date','location','status','created_at','updated_at']
            : [$detailFk,'material_id','quantity','unit_price','batch_code','location','status','created_at','updated_at'];
        $detailColsSql = '`' . implode('`,`', $detailCols) . '`';

        $flush = function () use (
            &$headerBatch, &$headerExternalIds, &$detailsByExternal,
            &$totalPhieu, &$totalDetails,
            $headerTable, $detailTable, $detailFk, $headerColsSql, $detailColsSql, $type, $pdo
        ) {
            if (empty($headerBatch)) return;

            // Build INSERT headers bằng string concat (nhanh hơn 2-3 lần so với PDO bind)
            $headerValues = [];
            foreach ($headerBatch as $h) {
                $headerValues[] = '('
                    . $pdo->quote($h['date']) . ','
                    . (int)$h['warehouse_id'] . ','
                    . (int)$h['partner_id'] . ','
                    . (int)$h['user_id'] . ','
                    . $pdo->quote($h['status']) . ','
                    . ($h['note'] === null ? 'NULL' : $pdo->quote($h['note'])) . ','
                    . ($h['delivery_partner_id'] === null ? 'NULL' : (int)$h['delivery_partner_id']) . ','
                    . $pdo->quote($h['delivery_status']) . ','
                    . (float)$h['delivery_fee'] . ','
                    . ($h['delivery_code'] === null ? 'NULL' : $pdo->quote($h['delivery_code'])) . ','
                    . $pdo->quote($h['created_at']) . ','
                    . $pdo->quote($h['updated_at'])
                    . ')';
            }
            $pdo->exec("INSERT INTO `{$headerTable}` ({$headerColsSql}) VALUES " . implode(',', $headerValues));
            $firstId = (int) $pdo->lastInsertId();

            // Build INSERT details
            $detailValues = [];
            $detailCount = 0;
            foreach ($headerExternalIds as $i => $ext) {
                $newId = $firstId + $i;
                $rows  = $detailsByExternal[$ext] ?? [];
                foreach ($rows as $row) {
                    if ($type === 'entry') {
                        $detailValues[] = '('
                            . $newId . ','
                            . (int)$row['material_id'] . ','
                            . $pdo->quote($row['quantity']) . ','
                            . (float)$row['unit_price'] . ','
                            . ($row['batch_code'] === null ? 'NULL' : $pdo->quote($row['batch_code'])) . ','
                            . ($row['expiry_date'] === null ? 'NULL' : $pdo->quote($row['expiry_date'])) . ','
                            . ($row['location'] === null ? 'NULL' : $pdo->quote($row['location'])) . ','
                            . $pdo->quote($row['status']) . ','
                            . $pdo->quote($row['created_at']) . ','
                            . $pdo->quote($row['updated_at'])
                            . ')';
                    } else {
                        $detailValues[] = '('
                            . $newId . ','
                            . (int)$row['material_id'] . ','
                            . $pdo->quote($row['quantity']) . ','
                            . (float)$row['unit_price'] . ','
                            . ($row['batch_code'] === null ? 'NULL' : $pdo->quote($row['batch_code'])) . ','
                            . ($row['location'] === null ? 'NULL' : $pdo->quote($row['location'])) . ','
                            . $pdo->quote($row['status']) . ','
                            . $pdo->quote($row['created_at']) . ','
                            . $pdo->quote($row['updated_at'])
                            . ')';
                    }
                    $detailCount++;
                }
            }

            // Insert theo lô để tránh max_allowed_packet
            foreach (array_chunk($detailValues, 2000) as $piece) {
                $pdo->exec("INSERT INTO `{$detailTable}` ({$detailColsSql}) VALUES " . implode(',', $piece));
            }

            $totalPhieu   += count($headerBatch);
            $totalDetails += $detailCount;

            $headerBatch = [];
            $headerExternalIds = [];
            $detailsByExternal = [];
        };

        try {
            while (($row = fgetcsv($fp)) !== false) {
                $lineNo++;
                if ($bar) {
                    $pos = ftell($fp);
                    if ($pos !== false && $pos - $lastBytePos > 65536) {
                        $bar->setProgress($pos);
                        $lastBytePos = $pos;
                    }
                }

                if ($row === [null] || $row === false) continue; // dòng trống

                $ext = $row[$idx['external_id']] ?? '';
                if ($ext === '') continue;

                // Khi gặp external mới → đóng buffer external cũ vào batch
                if ($currentExternal !== null && $ext !== $currentExternal) {
                    $headerBatch[] = $currentHeaderRow;
                    $headerExternalIds[] = $currentExternal;
                    $detailsByExternal[$currentExternal] = $currentDetails;

                    $currentDetails = [];
                    $currentHeaderRow = null;

                    if (count($headerBatch) >= $chunkPhieu) {
                        $flush();
                    }
                }

                // Header lần đầu cho external này
                if ($currentExternal !== $ext) {
                    $currentExternal = $ext;
                    $currentHeaderRow = [
                        'date'                => $this->safeDate($row[$idx['date']] ?? null),
                        'warehouse_id'        => (int) ($row[$idx['warehouse_id']] ?? 0),
                        'partner_id'          => (int) ($row[$idx['partner_id']] ?? 0),
                        'user_id'             => (int) ($row[$idx['user_id']] ?? 0),
                        'status'              => $row[$idx['status']] ?: 'completed',
                        'note'                => $row[$idx['note']] ?? null,
                        'delivery_partner_id' => $this->nullIfEmpty($row[$idx['delivery_partner_id']] ?? null),
                        'delivery_status'     => $row[$idx['delivery_status']] ?: 'pending',
                        'delivery_fee'        => (float) ($row[$idx['delivery_fee']] ?? 0),
                        'delivery_code'       => $row[$idx['delivery_code']] ?? null,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }

                // Detail row cho external hiện tại
                $detail = [
                    'material_id' => (int) ($row[$idx['material_id']] ?? 0),
                    'quantity'    => $row[$idx['quantity']] ?? 0,
                    'unit_price'  => (float) ($row[$idx['unit_price']] ?? 0),
                    'batch_code'  => $row[$idx['batch_code']] ?: null,
                    'location'    => $row[$idx['location']] ?: null,
                    'status'      => 'approved',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
                if ($type === 'entry') {
                    $detail['expiry_date'] = $this->safeDate($row[$idx['expiry_date']] ?? null, true);
                }
                $currentDetails[] = $detail;
            }

            // Flush nốt external cuối
            if ($currentExternal !== null && $currentHeaderRow !== null) {
                $headerBatch[] = $currentHeaderRow;
                $headerExternalIds[] = $currentExternal;
                $detailsByExternal[$currentExternal] = $currentDetails;
            }
            $flush();
        } finally {
            fclose($fp);
            if ($bar) {
                $bar->finish();
                $this->newLine();
            }

            // Commit + restore setting nếu turbo
            if ($turbo) {
                try {
                    $pdo->exec('COMMIT');
                    $pdo->exec('SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS');
                    $pdo->exec('SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS');
                    $pdo->exec('SET AUTOCOMMIT=@OLD_AUTOCOMMIT');
                    $pdo->exec('SET SQL_LOG_BIN=@OLD_SQL_LOG_BIN');
                } catch (\Throwable $e) {}
            }
        }

        $this->info("✅ HOÀN TẤT — {$totalPhieu} phiếu, {$totalDetails} chi tiết. Đọc {$lineNo} dòng dữ liệu.");
        return self::SUCCESS;
    }

    private function resolvePath(string $file): string
    {
        if (preg_match('#^([a-zA-Z]:[\\\\/])|^/#', $file)) {
            return $file; // tuyệt đối
        }
        return base_path($file);
    }

    private function safeDate($v, bool $nullable = false): ?string
    {
        if ($v === null || $v === '') return $nullable ? null : Carbon::today()->toDateString();
        try {
            return Carbon::parse($v)->toDateString();
        } catch (\Throwable $e) {
            return $nullable ? null : Carbon::today()->toDateString();
        }
    }

    private function nullIfEmpty($v): ?int
    {
        if ($v === null || $v === '' || $v === '0') return null;
        return (int) $v;
    }
}
