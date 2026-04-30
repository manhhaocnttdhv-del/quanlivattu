<?php

namespace App\Http\Controllers;

use App\Models\InventoryCheck;
use App\Models\Warehouse;
use App\Models\MaterialWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryCheckController extends Controller
{
    public function index()
    {
        $query = InventoryCheck::with(['warehouse', 'user'])->latest();
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', Auth::user()->warehouse_id);
        }
        $checks = $query->paginate(10);
        return view('inventory_checks.index', compact('checks'));
    }

    public function create(Request $request)
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        if (Auth::user()->role !== 'Admin tổng') {
            $warehouses = $warehouses->where('id', Auth::user()->warehouse_id);
        }

        // Selected warehouse for checking
        $selectedWarehouseId = $request->get('warehouse_id');
        $materials = [];

        if ($selectedWarehouseId) {
            $materials = MaterialWarehouse::where('warehouse_id', $selectedWarehouseId)
                ->with('material.unit')
                ->get();
        }

        return view('inventory_checks.create', compact('warehouses', 'materials', 'selectedWarehouseId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.system_stock' => 'required|numeric',
            'items.*.actual_stock' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $check = InventoryCheck::create([
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => Auth::id(),
                'date' => $validated['date'],
                'status' => 'pending',
                'note' => $validated['note']
            ]);

            foreach ($validated['items'] as $item) {
                $check->details()->create([
                    'material_id' => $item['material_id'],
                    'system_stock' => $item['system_stock'],
                    'actual_stock' => $item['actual_stock'],
                    'variance' => $item['actual_stock'] - $item['system_stock'],
                ]);
            }

            DB::commit();
            return redirect()->route('inventory-checks.index')->with('success', 'Tạo phiếu kiểm kê thành công! Đang chờ duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function show(InventoryCheck $inventoryCheck)
    {
        $inventoryCheck->load(['warehouse', 'user', 'details.material.unit']);
        return view('inventory_checks.show', compact('inventoryCheck'));
    }

    public function approve(InventoryCheck $inventoryCheck, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryCheck->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt phiếu đang chờ.');
        }

        try {
            DB::beginTransaction();

            $supplierId = \App\Models\Supplier::first()->id ?? 1; // Generic fallbacks for adjustments
            $projectId = \App\Models\Project::first()->id ?? 1; 

            // Create Adjustments if variance exists
            $positiveVariances = []; // Needs Entry
            $negativeVariances = []; // Needs Exit

            foreach ($inventoryCheck->details as $detail) {
                if ($detail->variance > 0) {
                    $positiveVariances[] = [
                        'material_id' => $detail->material_id,
                        'quantity' => $detail->variance
                    ];
                } elseif ($detail->variance < 0) {
                    $negativeVariances[] = [
                        'material_id' => $detail->material_id,
                        'quantity' => abs($detail->variance)
                    ];
                }
            }

            // Generate auto-entry
            if (count($positiveVariances) > 0) {
                $entry = \App\Models\InventoryEntry::create([
                    'date' => now(), 'warehouse_id' => $inventoryCheck->warehouse_id,
                    'supplier_id' => $supplierId, 'user_id' => Auth::id(),
                    'status' => 'completed', 'note' => 'Điều chỉnh kho (Kiểm kê #' . $inventoryCheck->id . ')'
                ]);
                foreach ($positiveVariances as $item) {
                    $entry->details()->create($item);
                    $inventoryService->updateStock($inventoryCheck->warehouse_id, $item['material_id'], $item['quantity'], 'add');
                }
            }

            // Generate auto-exit
            if (count($negativeVariances) > 0) {
                $exit = \App\Models\InventoryExit::create([
                    'date' => now(), 'warehouse_id' => $inventoryCheck->warehouse_id,
                    'project_id' => $projectId, 'user_id' => Auth::id(),
                    'status' => 'completed', 'note' => 'Điều chỉnh kho (Kiểm kê #' . $inventoryCheck->id . ')'
                ]);
                foreach ($negativeVariances as $item) {
                    $exit->details()->create($item);
                    $inventoryService->updateStock($inventoryCheck->warehouse_id, $item['material_id'], $item['quantity'], 'subtract');
                }
            }

            $inventoryCheck->update(['status' => 'completed']);
            DB::commit();
            
            return back()->with('success', 'Đã duyệt phiếu kiểm kê! Hệ thống tự động điều chỉnh số lượng tồn kho theo thực tế.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi duyệt: ' . $e->getMessage());
        }
    }

    public function cancel(InventoryCheck $inventoryCheck)
    {
        if ($inventoryCheck->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy phiếu kiểm kê ĐANG CHỜ (chưa duyệt). Nếu đã duyệt, bạn phải tự hủy Phiếu nhập/xuất điều chỉnh tự động.');
        }

        $inventoryCheck->update(['status' => 'cancelled']);
        return back()->with('success', 'Đã hủy phiếu kiểm kê.');
    }

    public function edit(string $id)
    {
        return redirect()->route('inventory-checks.index')->with('error', 'Không được phép sửa.');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        return redirect()->route('inventory-checks.index')->with('error', 'Sử dụng nút Hủy phiếu thay vì xóa.');
    }
}
