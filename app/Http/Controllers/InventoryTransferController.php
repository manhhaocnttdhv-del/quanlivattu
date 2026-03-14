<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransfer;
use App\Models\Warehouse;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryTransferController extends Controller
{
    public function index()
    {
        $query = InventoryTransfer::with(['fromWarehouse', 'toWarehouse', 'user'])->latest();
        if (Auth::user()->role !== 'Admin tổng') {
            $query->where(function($q) {
                $q->where('from_warehouse_id', Auth::user()->warehouse_id)
                  ->orWhere('to_warehouse_id', Auth::user()->warehouse_id);
            });
        }
        $transfers = $query->paginate(10);
        return view('inventory_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $allWarehouses = Warehouse::where('status', 'active')->get();
        
        // Sender warehouse (From)
        $fromWarehouses = $allWarehouses;
        if (Auth::user()->role !== 'Admin tổng') {
            $fromWarehouses = $allWarehouses->where('id', Auth::user()->warehouse_id);
        }

        // Receiver warehouse (To) could be any other active warehouse
        $toWarehouses = $allWarehouses;

        $materials = Material::with('unit')->get();

        return view('inventory_transfers.create', compact('fromWarehouses', 'toWarehouses', 'materials'));
    }

    public function store(Request $request, \App\Services\InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'note' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $transfer = InventoryTransfer::create([
                'date' => $validated['date'],
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'user_id' => Auth::id(),
                'status' => 'pending',
                'note' => $validated['note'],
            ]);

            foreach ($validated['materials'] as $item) {
                // Validate stock locally before allowing creation, even as pending
                $currentStock = $inventoryService->getStock($validated['from_warehouse_id'], $item['id']);
                if ($currentStock < $item['quantity']) {
                    throw new \Exception("Vật tư ID {$item['id']} không đủ tồn kho (hiện tại: {$currentStock}, yêu cầu: {$item['quantity']}) tại kho Nguồn.");
                }

                $transfer->details()->create([
                    'material_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('inventory-transfers.index')->with('success', 'Tạo phiếu chuyển kho thành công! Phiếu đang chờ duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(InventoryTransfer $inventoryTransfer)
    {
        $inventoryTransfer->load(['fromWarehouse', 'toWarehouse', 'user', 'details.material.unit']);
        return view('inventory_transfers.show', compact('inventoryTransfer'));
    }

    public function approve(InventoryTransfer $inventoryTransfer, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryTransfer->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt phiếu đang chờ.');
        }

        try {
            DB::beginTransaction();

            foreach ($inventoryTransfer->details as $detail) {
                // Subtract from source
                $inventoryService->updateStock(
                    $inventoryTransfer->from_warehouse_id,
                    $detail->material_id,
                    $detail->quantity,
                    'subtract'
                );

                // Add to destination
                $inventoryService->updateStock(
                    $inventoryTransfer->to_warehouse_id,
                    $detail->material_id,
                    $detail->quantity,
                    'add'
                );
            }

            $inventoryTransfer->update(['status' => 'completed']);

            DB::commit();
            return back()->with('success', 'Đã duyệt chuyển kho và điều chỉnh tồn!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi duyệt: ' . $e->getMessage());
        }
    }

    public function cancel(InventoryTransfer $inventoryTransfer, \App\Services\InventoryService $inventoryService)
    {
        if ($inventoryTransfer->status === 'cancelled') {
            return back()->with('error', 'Phiếu đã bị hủy.');
        }

        try {
            DB::beginTransaction();

            // If it was completed, we need to reverse both operations
            if ($inventoryTransfer->status === 'completed') {
                foreach ($inventoryTransfer->details as $detail) {
                    // Add back to source
                    $inventoryService->updateStock(
                        $inventoryTransfer->from_warehouse_id,
                        $detail->material_id,
                        $detail->quantity,
                        'add'
                    );

                    // Subtract from destination
                    $inventoryService->updateStock(
                        $inventoryTransfer->to_warehouse_id,
                        $detail->material_id,
                        $detail->quantity,
                        'subtract'
                    );
                }
            }

            $inventoryTransfer->update(['status' => 'cancelled']);

            DB::commit();
            return back()->with('success', 'Đã hủy phiếu chuyển kho thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi hủy: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        return redirect()->route('inventory-transfers.index')->with('error', 'Không được sửa phiếu chuyển kho.');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        return redirect()->route('inventory-transfers.index')->with('error', 'Vui lòng sử dụng tính năng Hủy Phiếu thay vì xóa vĩnh viễn.');
    }
}
