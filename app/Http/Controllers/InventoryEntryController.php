<?php

namespace App\Http\Controllers;

use App\Models\InventoryEntry;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryEntryController extends Controller
{
    public function index()
    {
        // Require role checks later, load basic for now
        $entries = InventoryEntry::with(['warehouse', 'supplier', 'user'])->latest()->paginate(10);
        return view('inventory_entries.index', compact('entries'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        
        // If not Admin tổng, only show their assigned warehouse
        if (Auth::user()->role !== 'Admin tổng') {
            $warehouses = $warehouses->where('id', Auth::user()->warehouse_id);
        }

        $suppliers = Supplier::all();
        $materials = Material::with('unit')->get();

        return view('inventory_entries.create', compact('warehouses', 'suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'note' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $entry = InventoryEntry::create([
                'date' => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'status' => 'completed', // Simplified for now
                'note' => $validated['note'],
            ]);

            foreach ($validated['materials'] as $item) {
                $entry->details()->create([
                    'material_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                ]);
                
                // Note: Stock update logic should go here, but keeping it simple for now or using observers
            }

            DB::commit();
            return redirect()->route('inventory-entries.index')->with('success', 'Tạo phiếu nhập thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(InventoryEntry $inventoryEntry)
    {
        $inventoryEntry->load(['warehouse', 'supplier', 'user', 'details.material.unit']);
        return view('inventory_entries.show', compact('inventoryEntry'));
    }

    public function edit(string $id)
    {
        return redirect()->route('inventory-entries.index')->with('error', 'Không được phép sửa phiếu nhập đã hoàn thành.');
    }

    public function update(Request $request, string $id)
    {
        // Typically not allowed to update completed entries
    }

    public function destroy(string $id)
    {
        // Complex logic needed to reverse stock, ignoring for basic implementation
        return redirect()->route('inventory-entries.index')->with('error', 'Chức năng xóa phiếu nhập đang được xây dựng.');
    }
}
