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
            'total_value' => \DB::table('material_warehouses')
                ->when($user->role !== 'Admin tổng', function($q) use ($user) {
                    return $q->where('warehouse_id', $user->warehouse_id);
                })
                ->selectRaw('SUM(stock * average_cost) as total')
                ->value('total') ?? 0
        ];

        // Basic recent activity
        $recent_entries_qb = InventoryEntry::with('warehouse', 'supplier')->latest()->take(5);
        $recent_exits_qb = InventoryExit::with('warehouse', 'project')->latest()->take(5);
        
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
            ->select('materials.name as material_name', 'materials.min_stock', 'warehouses.name as warehouse_name', 'material_warehouses.stock', 'material_warehouses.location')
            ->whereRaw('material_warehouses.stock < materials.min_stock');

        if ($user && $user->role !== 'Admin tổng') {
            $lowStockQuery->where('material_warehouses.warehouse_id', $user->warehouse_id);
        }

        $lowStockItems = $lowStockQuery->get();

        // 7-day Trend Data
        $days = [];
        $entryCounts = [];
        $exitCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
            $days[] = \Carbon\Carbon::now()->subDays($i)->format('d/m');
            
            $dayEntries = InventoryEntry::whereDate('date', $date);
            $dayExits = InventoryExit::whereDate('date', $date);
            
            if ($user && $user->role !== 'Admin tổng') {
                $dayEntries->where('warehouse_id', $user->warehouse_id);
                $dayExits->where('warehouse_id', $user->warehouse_id);
            }
            
            $entryCounts[] = $dayEntries->where('status', 'completed')->count();
            $exitCounts[] = $dayExits->where('status', 'completed')->count();
        }

        // Warehouse Distribution (Stock)
        $warehouseStock = \DB::table('material_warehouses')
            ->join('warehouses', 'material_warehouses.warehouse_id', '=', 'warehouses.id')
            ->select('warehouses.name', \DB::raw('SUM(material_warehouses.stock) as total_stock'))
            ->groupBy('warehouses.id', 'warehouses.name');
            
        if ($user && $user->role !== 'Admin tổng') {
            $warehouseStock->where('warehouses.id', $user->warehouse_id);
        }
        
        $stockDist = $warehouseStock->get();

        return view('dashboard', compact(
            'stats', 
            'recent_entries', 
            'recent_exits', 
            'lowStockItems',
            'days',
            'entryCounts',
            'exitCounts',
            'stockDist'
        ));
    }
}
