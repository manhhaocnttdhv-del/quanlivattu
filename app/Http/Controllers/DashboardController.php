<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Material;
use App\Models\InventoryEntry;
use App\Models\InventoryExit;
use App\Models\InventoryTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Value of stock
        $totalStockValue = DB::table('material_warehouses')
            ->when($user->role !== 'Admin tổng', function($q) use ($user) {
                return $q->where('warehouse_id', $user->warehouse_id);
            })
            ->selectRaw('SUM(stock * average_cost) as total')
            ->value('total') ?? 0;

        // Total import value (approved items in completed entries)
        $totalImportValueQuery = DB::table('inventory_entry_details')
            ->join('inventory_entries', 'inventory_entry_details.inventory_entry_id', '=', 'inventory_entries.id')
            ->where('inventory_entries.status', 'completed')
            ->where('inventory_entry_details.status', 'approved');
        if ($user && $user->role !== 'Admin tổng') {
            $totalImportValueQuery->where('inventory_entries.warehouse_id', $user->warehouse_id);
        }
        $totalImportValue = $totalImportValueQuery->selectRaw('SUM(inventory_entry_details.quantity * inventory_entry_details.unit_price) as total')->value('total') ?? 0;

        // Total export value (approved items in completed exits)
        $totalExportValueQuery = DB::table('inventory_exit_details')
            ->join('inventory_exits', 'inventory_exit_details.inventory_exit_id', '=', 'inventory_exits.id')
            ->where('inventory_exits.status', 'completed')
            ->where('inventory_exit_details.status', 'approved');
        if ($user && $user->role !== 'Admin tổng') {
            $totalExportValueQuery->where('inventory_exits.warehouse_id', $user->warehouse_id);
        }
        $totalExportValue = $totalExportValueQuery->selectRaw('SUM(inventory_exit_details.quantity * inventory_exit_details.unit_price) as total')->value('total') ?? 0;

        // Total shipping fees
        $entryDeliveryFeeQuery = InventoryEntry::where('status', 'completed');
        $exitDeliveryFeeQuery = InventoryExit::where('status', 'completed');
        $transferDeliveryFeeQuery = InventoryTransfer::where('status', 'completed');
        if ($user && $user->role !== 'Admin tổng') {
            $entryDeliveryFeeQuery->where('warehouse_id', $user->warehouse_id);
            $exitDeliveryFeeQuery->where('warehouse_id', $user->warehouse_id);
            $transferDeliveryFeeQuery->where(function($q) use ($user) {
                $q->where('from_warehouse_id', $user->warehouse_id)
                  ->orWhere('to_warehouse_id', $user->warehouse_id);
            });
        }
        $totalDeliveryFees = $entryDeliveryFeeQuery->sum('delivery_fee') + $exitDeliveryFeeQuery->sum('delivery_fee') + $transferDeliveryFeeQuery->sum('delivery_fee');

        $stats = [
            'total_warehouses' => $warehouses->count(),
            'total_materials' => Material::count(),
            'total_entries' => $entries->count(),
            'total_exits' => $exits->count(),
            'total_value' => $totalStockValue,
            'total_import_value' => $totalImportValue,
            'total_export_value' => $totalExportValue,
            'total_delivery_fees' => $totalDeliveryFees,
            'active_delivery_partners' => \App\Models\DeliveryPartner::where('status', 'active')->count()
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
        $lowStockQuery = DB::table('material_warehouses')
            ->join('materials', 'material_warehouses.material_id', '=', 'materials.id')
            ->join('warehouses', 'material_warehouses.warehouse_id', '=', 'warehouses.id')
            ->select('materials.name as material_name', 'materials.min_stock', 'warehouses.name as warehouse_name', 'material_warehouses.stock', 'material_warehouses.location')
            ->whereRaw('material_warehouses.stock < materials.min_stock');

        if ($user && $user->role !== 'Admin tổng') {
            $lowStockQuery->where('material_warehouses.warehouse_id', $user->warehouse_id);
        }

        $lowStockItems = $lowStockQuery->get();

        // 7-day Trend Data (Values instead of counts)
        $days = [];
        $entryValues = [];
        $exitValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
            $days[] = \Carbon\Carbon::now()->subDays($i)->format('d/m');
            
            $dayEntries = DB::table('inventory_entry_details')
                ->join('inventory_entries', 'inventory_entry_details.inventory_entry_id', '=', 'inventory_entries.id')
                ->where('inventory_entries.status', 'completed')
                ->where('inventory_entry_details.status', 'approved')
                ->whereDate('inventory_entries.date', $date);
                
            $dayExits = DB::table('inventory_exit_details')
                ->join('inventory_exits', 'inventory_exit_details.inventory_exit_id', '=', 'inventory_exits.id')
                ->where('inventory_exits.status', 'completed')
                ->where('inventory_exit_details.status', 'approved')
                ->whereDate('inventory_exits.date', $date);
            
            if ($user && $user->role !== 'Admin tổng') {
                $dayEntries->where('inventory_entries.warehouse_id', $user->warehouse_id);
                $dayExits->where('inventory_exits.warehouse_id', $user->warehouse_id);
            }
            
            $entryValues[] = $dayEntries->selectRaw('SUM(inventory_entry_details.quantity * inventory_entry_details.unit_price) as total')->value('total') ?? 0;
            $exitValues[] = $dayExits->selectRaw('SUM(inventory_exit_details.quantity * inventory_exit_details.unit_price) as total')->value('total') ?? 0;
        }

        // Warehouse Distribution (Stock)
        $warehouseStock = DB::table('material_warehouses')
            ->join('warehouses', 'material_warehouses.warehouse_id', '=', 'warehouses.id')
            ->select('warehouses.name', DB::raw('SUM(material_warehouses.stock) as total_stock'))
            ->groupBy('warehouses.id', 'warehouses.name');
            
        if ($user && $user->role !== 'Admin tổng') {
            $warehouseStock->where('warehouses.id', $user->warehouse_id);
        }
        
        $stockDist = $warehouseStock->get();

        // Delivery Status Distribution
        $statuses = ['pending' => 0, 'in_transit' => 0, 'delivered' => 0, 'failed' => 0];
        
        $exitShipments = DB::table('inventory_exits')->select('delivery_status', DB::raw('count(*) as count'))->whereNotNull('delivery_partner_id');
        $entryShipments = DB::table('inventory_entries')->select('delivery_status', DB::raw('count(*) as count'))->whereNotNull('delivery_partner_id');
        $transferShipments = DB::table('inventory_transfers')->select('delivery_status', DB::raw('count(*) as count'))->whereNotNull('delivery_partner_id');

        if ($user && $user->role !== 'Admin tổng') {
            $exitShipments->where('warehouse_id', $user->warehouse_id);
            $entryShipments->where('warehouse_id', $user->warehouse_id);
            $transferShipments->where(function($q) use ($user) {
                $q->where('from_warehouse_id', $user->warehouse_id)
                  ->orWhere('to_warehouse_id', $user->warehouse_id);
            });
        }

        foreach ($exitShipments->groupBy('delivery_status')->get() as $row) {
            if (array_key_exists($row->delivery_status, $statuses)) {
                $statuses[$row->delivery_status] += $row->count;
            }
        }
        foreach ($entryShipments->groupBy('delivery_status')->get() as $row) {
            if (array_key_exists($row->delivery_status, $statuses)) {
                $statuses[$row->delivery_status] += $row->count;
            }
        }
        foreach ($transferShipments->groupBy('delivery_status')->get() as $row) {
            if (array_key_exists($row->delivery_status, $statuses)) {
                $statuses[$row->delivery_status] += $row->count;
            }
        }

        // Recent Shipments
        $recentExitsShip = InventoryExit::with('deliveryPartner', 'warehouse', 'project')
            ->whereNotNull('delivery_partner_id')
            ->latest()
            ->take(3);
        $recentEntriesShip = InventoryEntry::with('deliveryPartner', 'warehouse', 'supplier')
            ->whereNotNull('delivery_partner_id')
            ->latest()
            ->take(3);
        
        if ($user && $user->role !== 'Admin tổng') {
            $recentExitsShip->where('warehouse_id', $user->warehouse_id);
            $recentEntriesShip->where('warehouse_id', $user->warehouse_id);
        }
        
        $recentShipments = collect()
            ->merge($recentExitsShip->get()->map(fn($item) => [
                'type' => 'Xuất kho',
                'id' => 'PX-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'date' => $item->date,
                'partner' => $item->deliveryPartner->name ?? 'N/A',
                'code' => $item->delivery_code ?? 'N/A',
                'fee' => $item->delivery_fee,
                'status' => $item->delivery_status,
                'url' => route('inventory-exits.show', $item->id)
            ]))
            ->merge($recentEntriesShip->get()->map(fn($item) => [
                'type' => 'Nhập kho',
                'id' => 'PN-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                'date' => $item->date,
                'partner' => $item->deliveryPartner->name ?? 'N/A',
                'code' => $item->delivery_code ?? 'N/A',
                'fee' => $item->delivery_fee,
                'status' => $item->delivery_status,
                'url' => route('inventory-entries.show', $item->id)
            ]))
            ->sortByDesc('date')
            ->take(5);

        return view('dashboard', compact(
            'stats', 
            'recent_entries', 
            'recent_exits', 
            'lowStockItems',
            'days',
            'entryValues',
            'exitValues',
            'stockDist',
            'statuses',
            'recentShipments'
        ));
    }
}
