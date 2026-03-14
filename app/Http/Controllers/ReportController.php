<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function inventory(Request $request)
    {
        $user = auth()->user();
        
        $query = \App\Models\MaterialWarehouse::with(['material.unit', 'warehouse']);

        if ($user && $user->role !== 'Admin tổng') {
            $query->where('warehouse_id', $user->warehouse_id);
        } else {
            // Admin tổng có thể lọc theo kho (tùy chọn)
            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }
        }

        $stockData = $query->get();

        $warehouses = [];
        if ($user && $user->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }

        return view('reports.inventory', compact('stockData', 'warehouses'));
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventoryReportExport($request->warehouse_id), 
            'bao-cao-ton-kho-' . date('Ymd-Hi') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\MaterialWarehouse::with(['material.unit', 'warehouse']);

        if ($user && $user->role !== 'Admin tổng') {
            $query->where('warehouse_id', $user->warehouse_id);
        } else {
            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }
        }

        $stockData = $query->get();
        $warehouseName = 'Tất cả các kho';
        if ($request->filled('warehouse_id')) {
            $wh = \App\Models\Warehouse::find($request->warehouse_id);
            if ($wh) $warehouseName = $wh->name;
        } elseif ($user && $user->role !== 'Admin tổng') {
            $warehouseName = $user->warehouse->name ?? 'Kho cá nhân';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.inventory_pdf', compact('stockData', 'warehouseName'));
        
        return $pdf->download('bao-cao-ton-kho-' . date('Ymd-Hi') . '.pdf');
    }
}
