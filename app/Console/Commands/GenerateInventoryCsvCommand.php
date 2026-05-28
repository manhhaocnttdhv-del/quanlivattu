<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * Sinh file CSV / XLSX mẫu để import qua command `inventory:import-csv`.
 *
 * Mặc định CSV (nhanh, nhẹ, mở bằng Excel được). Dùng --xlsx nếu muốn .xlsx thực sự.
 *
 * Ví dụ:
 *   php artisan inventory:generate-csv --rows=100000 --type=entry
 *   php artisan inventory:generate-csv --rows=100000 --type=exit --xlsx
 *
 * "rows" = số PHIẾU. Mỗi phiếu có 1-3 dòng chi tiết (tỷ lệ ngẫu nhiên).
 */
class GenerateInventoryCsvCommand extends Command
{
    protected $signature = 'inventory:generate-csv
                            {--rows=100000 : Số phiếu cần sinh}
                            {--type=entry : entry|exit}
                            {--out= : Đường dẫn file đầu ra (mặc định storage/app/imports/<type>_<rows>.csv)}
                            {--xlsx : Xuất ra file .xlsx (chậm + tốn RAM hơn)}
                            {--lines-per-row=2 : Số chi tiết trung bình mỗi phiếu}';

    protected $description = 'Sinh file CSV/XLSX mẫu cho import phiếu nhập / xuất kho';

    public function handle(): int
    {
        $rows = (int) $this->option('rows');
        $type = $this->option('type');
        $useXlsx = (bool) $this->option('xlsx');
        $linesAvg = max(1, (int) $this->option('lines-per-row'));

        if (! in_array($type, ['entry', 'exit'])) {
            $this->error('--type chỉ chấp nhận entry|exit'); return self::FAILURE;
        }
        if ($rows <= 0) {
            $this->error('--rows phải > 0'); return self::FAILURE;
        }

        // Lấy id thực tế trong DB
        $whIds   = DB::table('warehouses')->pluck('id')->all();
        $supIds  = DB::table('suppliers')->pluck('id')->all();
        $projIds = DB::table('projects')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();
        $matIds  = DB::table('materials')->pluck('id')->all();
        $dpIds   = DB::table('delivery_partners')->pluck('id')->all();

        if (empty($whIds) || empty($matIds) || empty($userIds) || ($type==='entry'?empty($supIds):empty($projIds))) {
            $this->error('Thiếu dữ liệu nền (warehouses / materials / users / suppliers|projects). Hãy seed cơ bản trước.');
            return self::FAILURE;
        }

        $ext = $useXlsx ? 'xlsx' : 'csv';
        $out = $this->option('out') ?: storage_path("app/imports/{$type}_{$rows}.{$ext}");
        @mkdir(dirname($out), 0775, true);

        $headers = [
            'external_id','date','warehouse_id','partner_id','user_id','status','note',
            'delivery_partner_id','delivery_status','delivery_fee','delivery_code',
            'material_id','quantity','unit_price','batch_code','expiry_date','location',
        ];

        $today = Carbon::today()->toDateString();
        $statusPool = ['completed','completed','completed','pending','cancelled'];
        $delPool    = ['delivered','delivered','in_transit','pending','failed'];

        $start = microtime(true);
        $bar = $this->output->createProgressBar($rows);
        $bar->setFormat(' %current%/%max% phiếu [%bar%] %percent:3s%%  %elapsed:6s% / %estimated:-6s%  Mem: %memory:6s%');
        $bar->start();

        $rowGen = function () use ($rows, $headers, $linesAvg, $whIds, $supIds, $projIds, $userIds, $matIds, $dpIds, $today, $statusPool, $delPool, $type, $bar) {
            for ($p = 1; $p <= $rows; $p++) {
                $w = $whIds[array_rand($whIds)];
                $partner = $type === 'entry' ? $supIds[array_rand($supIds)] : $projIds[array_rand($projIds)];
                $u = $userIds[array_rand($userIds)];
                $st = $statusPool[array_rand($statusPool)];
                $dp = !empty($dpIds) && random_int(0,1) ? $dpIds[array_rand($dpIds)] : '';
                $ds = $delPool[array_rand($delPool)];
                $df = random_int(0, 5_000_000);
                $dc = 'VD' . str_pad((string)$p, 7, '0', STR_PAD_LEFT);
                $note = "Auto-generated #{$p}";

                $lineCnt = max(1, random_int(max(1,$linesAvg-1), $linesAvg+1));
                for ($l = 0; $l < $lineCnt; $l++) {
                    yield [
                        $p, $today, $w, $partner, $u, $st, $note,
                        $dp, $ds, $df, $dc,
                        $matIds[array_rand($matIds)], random_int(1, 500), random_int(10000, 500000),
                        'B'.random_int(1000,9999), '', 'A'.random_int(1,20),
                    ];
                }

                if ($p % 1000 === 0) $bar->advance(1000);
            }
            // advance còn lại
            $rem = $rows % 1000;
            if ($rem > 0) $bar->advance($rem);
        };

        if ($useXlsx) {
            $this->writeXlsx($out, $headers, $rowGen());
        } else {
            $this->writeCsv($out, $headers, $rowGen());
        }

        $bar->finish();
        $this->newLine();
        $size = filesize($out);
        $this->info(sprintf(
            '✅ Tạo xong %s (%s) — %d phiếu — %s — %.1fs',
            $out,
            $this->humanBytes($size),
            $rows,
            $useXlsx ? 'XLSX' : 'CSV',
            microtime(true) - $start
        ));
        return self::SUCCESS;
    }

    private function writeCsv(string $path, array $headers, iterable $rows): void
    {
        $fp = fopen($path, 'wb');
        // BOM UTF-8 để Excel hiển thị tiếng Việt đúng
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, $headers);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    private function writeXlsx(string $path, array $headers, iterable $rows): void
    {
        // Dùng OpenSpout — streaming, RAM thấp, hỗ trợ vài triệu dòng dễ dàng.
        if (! class_exists(\OpenSpout\Writer\XLSX\Writer::class)) {
            throw new \RuntimeException('Thiếu package openspout/openspout. Chạy: composer require openspout/openspout');
        }

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($path);

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues($headers));
        foreach ($rows as $row) {
            // OpenSpout tự suy luận kiểu, nhưng cần force string cho code có thể lẫn số (như VD000001)
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(array_map(static function ($v) {
                return $v === null ? '' : $v;
            }, $row)));
        }
        $writer->close();
    }

    private function humanBytes(int $bytes): string
    {
        $units = ['B','KB','MB','GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units)-1) { $bytes /= 1024; $i++; }
        return number_format($bytes, 2) . ' ' . $units[$i];
    }
}
