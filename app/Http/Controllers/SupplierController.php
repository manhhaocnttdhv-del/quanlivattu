<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $query = Supplier::with('warehouse')->latest();

        if (auth()->check() && auth()->user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', auth()->user()->warehouse_id)->orWhereNull('warehouse_id');
        }

        $suppliers = $query->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $warehouses = [];
        if (auth()->check() && auth()->user()->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }
        return view('suppliers.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'name.required' => 'Tên nhà cung cấp không được để trống.',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại.',
        ]);

        if (auth()->user()->role !== 'Admin tổng') {
            $validated['warehouse_id'] = auth()->user()->warehouse_id;
        }

        Supplier::create($validated);
        return redirect()->route('suppliers.index')->with('success', 'Thêm nhà cung cấp thành công!');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $warehouses = [];
        if (auth()->check() && auth()->user()->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }
        return view('suppliers.edit', compact('supplier', 'warehouses'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'name.required' => 'Tên nhà cung cấp không được để trống.',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại.',
        ]);

        if (auth()->user()->role !== 'Admin tổng') {
            $validated['warehouse_id'] = auth()->user()->warehouse_id;
        }

        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Cập nhật nhà cung cấp thành công!');
    }

    public function destroy(Supplier $supplier)
    {
        // Add check if supplier is used in inventory entries before deletion
        if ($supplier->inventoryEntries()->count() > 0) {
            return redirect()->route('suppliers.index')->with('error', 'Không thể xóa nhà cung cấp đã có giao dịch!');
        }
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Xóa nhà cung cấp thành công!');
    }
}
