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
        $transfers = InventoryTransfer::with(['fromWarehouse', 'toWarehouse', 'user'])->latest()->paginate(10);
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

    public function store(Request $request)
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
                'status' => 'completed',
                'note' => $validated['note'],
            ]);

            foreach ($validated['materials'] as $item) {
                // Again, a real system checks stock in from_warehouse and updates both warehouses
                $transfer->details()->create([
                    'material_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('inventory-transfers.index')->with('success', 'Tạo phiếu chuyển kho thành công!');
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
        return redirect()->route('inventory-transfers.index')->with('error', 'Chức năng xóa phiếu chuyển kho đang được xây dựng.');
    }
}
