<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $query = Customer::with('warehouse')->latest();

        if (auth()->check() && auth()->user()->role !== 'Admin tổng') {
            $query->where('warehouse_id', auth()->user()->warehouse_id)->orWhereNull('warehouse_id');
        }

        $customers = $query->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $warehouses = [];
        if (auth()->check() && auth()->user()->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }
        return view('customers.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'name.required' => 'Tên khách hàng không được để trống.',
            'name.unique' => 'Tên khách hàng đã tồn tại.',
        ]);

        if (auth()->user()->role !== 'Admin tổng') {
            $validated['warehouse_id'] = auth()->user()->warehouse_id;
        }

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', 'Thêm khách hàng thành công!');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $warehouses = [];
        if (auth()->check() && auth()->user()->role === 'Admin tổng') {
            $warehouses = \App\Models\Warehouse::all();
        }
        return view('customers.edit', compact('customer', 'warehouses'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ], [
            'name.required' => 'Tên khách hàng không được để trống.',
            'name.unique' => 'Tên khách hàng đã tồn tại.',
        ]);

        if (auth()->user()->role !== 'Admin tổng') {
            $validated['warehouse_id'] = auth()->user()->warehouse_id;
        }

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function destroy(Customer $customer)
    {
        // Add check if customer is used in inventory exits before deletion
        if ($customer->inventoryExits()->count() > 0) {
            return redirect()->route('customers.index')->with('error', 'Không thể xóa khách hàng đã có giao dịch!');
        }
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Xóa khách hàng thành công!');
    }
}
