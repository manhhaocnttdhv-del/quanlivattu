<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryAlert;
use App\Models\MaterialWarehouse;
use App\Models\AppSetting;
use App\Models\Warehouse;

class InventoryAlertController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $defaultMin = (float) AppSetting::get('default_min_stock_level', 10);

        // Lấy tồn kho thực tế từ material_warehouses và tự sinh cảnh báo
        $query = MaterialWarehouse::with(['material.unit', 'warehouse'])
            ->whereRaw('stock < COALESCE((SELECT min_stock FROM materials WHERE id = material_id), ?)', [$defaultMin]);

        if (!$user->isAdminTong()) {
            $query->where('warehouse_id', $user->warehouse_id);
        } elseif ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $lowStockItems = $query->orderBy('stock', 'asc')->paginate(20)->appends($request->query());

        // Cảnh báo đã ghi nhận (đang chờ xử lý)
        $alertQuery = InventoryAlert::with(['material.unit', 'warehouse'])
            ->where('is_resolved', false);
        if (!$user->isAdminTong()) {
            $alertQuery->where('warehouse_id', $user->warehouse_id);
        }
        $pendingAlerts = $alertQuery->latest()->get();

        $warehouses = $user->isAdminTong() ? Warehouse::all() : collect();

        return view('inventory_alerts.index', compact('lowStockItems', 'pendingAlerts', 'warehouses', 'defaultMin'));
    }

    public function resolve(InventoryAlert $inventoryAlert)
    {
        $inventoryAlert->update(['is_resolved' => true]);
        return back()->with('success', 'Đã đánh dấu xử lý cảnh báo này.');
    }

    public function getLowStockAlerts()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['alerts' => []]);
        }

        $defaultMin = (float) AppSetting::get('default_min_stock_level', 10);

        $query = MaterialWarehouse::with(['material.unit', 'warehouse'])
            ->whereRaw('stock < COALESCE((SELECT min_stock FROM materials WHERE id = material_id), ?)', [$defaultMin]);

        if ($user->role !== 'Admin tổng') {
            if ($user->warehouse_id) {
                $query->where('warehouse_id', $user->warehouse_id);
            } else {
                return response()->json(['alerts' => []]);
            }
        }

        $lowStockItems = $query->get()->map(function($item) {
            return [
                'material_name' => $item->material->name ?? 'N/A',
                'warehouse_name' => $item->warehouse->name ?? 'N/A',
                'stock' => $item->stock,
                'min_stock' => $item->material->min_stock ?? 10,
                'unit' => $item->material->unit->name ?? '',
            ];
        });

        return response()->json([
            'alerts' => $lowStockItems
        ]);
    }
}
