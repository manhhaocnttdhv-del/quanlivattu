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
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('bao-cao-ton-kho-' . date('Ymd-Hi') . '.pdf');
    }

    /**
     * Báo cáo lịch sử nhập/xuất vật tư theo thời gian
     */
    public function materialHistory(Request $request)
    {
        $user = auth()->user();
        $materials = \App\Models\Material::with('unit')->orderBy('name')->get();
        $warehouses = ($user && $user->role === 'Admin tổng') ? \App\Models\Warehouse::all() : collect();

        $history = collect();
        $selectedMaterial = null;

        if ($request->filled('material_id') && $request->filled('date_from') && $request->filled('date_to')) {
            $materialId  = $request->material_id;
            $dateFrom    = $request->date_from;
            $dateTo      = $request->date_to;
            $warehouseId = $request->warehouse_id;

            $selectedMaterial = \App\Models\Material::with('unit')->find($materialId);

            // Lấy phiếu NHẬP
            $entries = \App\Models\InventoryEntryDetail::with(['inventoryEntry.warehouse'])
                ->where('material_id', $materialId)
                ->whereHas('inventoryEntry', function ($q) use ($dateFrom, $dateTo, $warehouseId, $user) {
                    $q->whereIn('status', ['completed'])
                      ->whereBetween('date', [$dateFrom, $dateTo]);
                    if ($warehouseId) $q->where('warehouse_id', $warehouseId);
                    elseif ($user->role !== 'Admin tổng') $q->where('warehouse_id', $user->warehouse_id);
                })
                ->get()
                ->map(fn($d) => [
                    'date'      => $d->inventoryEntry->date,
                    'type'      => 'Nhập kho',
                    'type_icon' => 'bi-arrow-down-circle-fill text-success',
                    'ref'       => 'PN-' . str_pad($d->inventoryEntry->id, 5, '0', STR_PAD_LEFT),
                    'warehouse' => $d->inventoryEntry->warehouse->name ?? '—',
                    'in'        => $d->quantity,
                    'out'       => 0,
                ]);

            // Lấy phiếu XUẤT (đã duyệt)
            $exits = \App\Models\InventoryExitDetail::with(['inventoryExit.warehouse'])
                ->where('material_id', $materialId)
                ->where('status', 'approved')
                ->whereHas('inventoryExit', function ($q) use ($dateFrom, $dateTo, $warehouseId, $user) {
                    $q->whereIn('status', ['completed', 'pending'])
                      ->whereBetween('date', [$dateFrom, $dateTo]);
                    if ($warehouseId) $q->where('warehouse_id', $warehouseId);
                    elseif ($user->role !== 'Admin tổng') $q->where('warehouse_id', $user->warehouse_id);
                })
                ->get()
                ->map(fn($d) => [
                    'date'      => $d->inventoryExit->date,
                    'type'      => 'Xuất kho',
                    'type_icon' => 'bi-arrow-up-circle-fill text-danger',
                    'ref'       => 'PX-' . str_pad($d->inventoryExit->id, 5, '0', STR_PAD_LEFT),
                    'warehouse' => $d->inventoryExit->warehouse->name ?? '—',
                    'in'        => 0,
                    'out'       => $d->quantity,
                ]);

            $history = $entries->merge($exits)->sortBy('date')->values();
        }

        return view('reports.material_history', compact('materials', 'warehouses', 'history', 'selectedMaterial'));
    }
}
