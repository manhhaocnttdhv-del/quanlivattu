<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

/**
 * Trang upload CSV phiếu nhập / xuất.
 * - File nhỏ → import luôn trong request.
 * - File lớn → khuyến nghị dùng CLI: `php artisan inventory:import-csv ...`
 */
class InventoryCsvImportController extends Controller
{
    public function form()
    {
        return view('inventory_csv.form');
    }

    public function template(Request $request)
    {
        $type = $request->query('type', 'entry');
        $filename = $type === 'exit' ? 'phieu_xuat_template.csv' : 'phieu_nhap_template.csv';

        $cols = [
            'external_id','date','warehouse_id','partner_id','user_id','status','note',
            'delivery_partner_id','delivery_status','delivery_fee','delivery_code',
            'material_id','quantity','unit_price','batch_code','expiry_date','location',
        ];
        $today = Carbon::today()->toDateString();
        $sample1 = ['1', $today, '1', '1', '1', 'completed', 'Phiếu mẫu 1', '', 'pending', '0', 'VD0001', '1', '100', '25000', 'B1001', '', 'A1'];
        $sample2 = ['1', $today, '1', '1', '1', 'completed', 'Phiếu mẫu 1', '', 'pending', '0', 'VD0001', '2', '50', '30000', 'B1001', '', 'A2'];

        $csv  = implode(',', $cols) . "\n";
        $csv .= implode(',', $sample1) . "\n";
        $csv .= implode(',', $sample2) . "\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:204800', // ~200MB
            'type' => 'required|in:entry,exit',
            'chunk' => 'nullable|integer|min:100|max:10000',
        ]);

        $type = $request->input('type');
        $chunk = (int) $request->input('chunk', 2000);

        // Lưu file vào storage/app/imports
        $stored = $request->file('file')->store('imports');
        $abs = storage_path('app/' . $stored);

        // Gọi artisan command — output capture
        $exitCode = Artisan::call('inventory:import-csv', [
            'file'      => $abs,
            '--type'    => $type,
            '--chunk'   => $chunk,
            '--no-progress' => true,
        ]);
        $output = Artisan::output();

        // Xóa file sau khi import xong
        @unlink($abs);

        if ($exitCode !== 0) {
            return back()->with('error', 'Import thất bại:\n' . $output);
        }
        return back()->with('success', 'Import xong:\n' . $output);
    }
}
