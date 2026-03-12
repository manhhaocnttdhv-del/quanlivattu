<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('manager')->latest()->paginate(10);
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $managers = User::where('role', 'Admin kho')->get();
        return view('warehouses.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses',
            'address' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        Warehouse::create($validated);
        return redirect()->route('warehouses.index')->with('success', 'Thêm kho hàng thành công!');
    }

    public function show(Warehouse $warehouse)
    {
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $managers = User::where('role', 'Admin kho')->get();
        return view('warehouses.edit', compact('warehouse', 'managers'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $warehouse->id,
            'address' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $warehouse->update($validated);
        return redirect()->route('warehouses.index')->with('success', 'Cập nhật kho hàng thành công!');
    }

    public function destroy(Warehouse $warehouse)
    {
        // Add check if warehouse has materials or transactions
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Xóa kho hàng thành công!');
    }
}
