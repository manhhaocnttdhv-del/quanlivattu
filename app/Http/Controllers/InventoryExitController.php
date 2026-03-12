<?php

namespace App\Http\Controllers;

use App\Models\InventoryExit;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryExitController extends Controller
{
    public function index()
    {
        $exits = InventoryExit::with(['warehouse', 'customer', 'user'])->latest()->paginate(10);
        return view('inventory_exits.index', compact('exits'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        if (Auth::user()->role !== 'Admin tổng') {
            $warehouses = $warehouses->where('id', Auth::user()->warehouse_id);
        }

        $customers = Customer::all();
        $materials = Material::with('unit')->get(); // Note: Ideally should check stock here, skipping for basic version

        return view('inventory_exits.create', compact('warehouses', 'customers', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'note' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $exit = InventoryExit::create([
                'date' => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'],
                'customer_id' => $validated['customer_id'],
                'user_id' => Auth::id(),
                'status' => 'completed',
                'note' => $validated['note'],
            ]);

            foreach ($validated['materials'] as $item) {
                // To keep it simple, we just create the detail without checking stock limits 
                // A real system MUST check if warehouse_id has enough stock of material_id
                $exit->details()->create([
                    'material_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('inventory-exits.index')->with('success', 'Tạo phiếu xuất thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(InventoryExit $inventoryExit)
    {
        $inventoryExit->load(['warehouse', 'customer', 'user', 'details.material.unit']);
        return view('inventory_exits.show', compact('inventoryExit'));
    }

    public function edit(string $id)
    {
        return redirect()->route('inventory-exits.index')->with('error', 'Không được sửa phiếu xuất.');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        return redirect()->route('inventory-exits.index')->with('error', 'Chức năng xóa phiếu xuất đang được xây dựng.');
    }
}
