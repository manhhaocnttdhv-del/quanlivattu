<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Material;
use App\Models\InventoryEntry;
use App\Models\InventoryExit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $warehouses = Warehouse::query();
        $entries = InventoryEntry::query();
        $exits = InventoryExit::query();
        
        if ($user && $user->role !== 'Admin tổng') {
            $warehouses->where('id', $user->warehouse_id);
            $entries->where('warehouse_id', $user->warehouse_id);
            $exits->where('warehouse_id', $user->warehouse_id);
        }

        $stats = [
            'total_warehouses' => $warehouses->count(),
            'total_materials' => Material::count(),
            'total_entries' => $entries->count(),
            'total_exits' => $exits->count(),
        ];

        // Basic recent activity
        $recent_entries_qb = InventoryEntry::with('warehouse', 'supplier')->latest()->take(5);
        $recent_exits_qb = InventoryExit::with('warehouse', 'customer')->latest()->take(5);
        
        if ($user && $user->role !== 'Admin tổng') {
            $recent_entries_qb->where('warehouse_id', $user->warehouse_id);
            $recent_exits_qb->where('warehouse_id', $user->warehouse_id);
        }
        
        $recent_entries = $recent_entries_qb->get();
        $recent_exits = $recent_exits_qb->get();

        // Low Stock Alerts
        $lowStockQuery = \DB::table('material_warehouses')
            ->join('materials', 'material_warehouses.material_id', '=', 'materials.id')
            ->join('warehouses', 'material_warehouses.warehouse_id', '=', 'warehouses.id')
            ->select('materials.name as material_name', 'materials.min_stock', 'warehouses.name as warehouse_name', 'material_warehouses.stock')
            ->whereRaw('material_warehouses.stock < materials.min_stock');

        if ($user && $user->role !== 'Admin tổng') {
            $lowStockQuery->where('material_warehouses.warehouse_id', $user->warehouse_id);
        }

        $lowStockItems = $lowStockQuery->get();

        return view('dashboard', compact('stats', 'recent_entries', 'recent_exits', 'lowStockItems'));
    }
}
