<?php

namespace App\Http\Controllers;

use App\Models\WarehouseStaff;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseStaffController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = WarehouseStaff::with(['warehouse'])->latest();

        // Nhân viên kho chỉ thấy kho của mình
        if (!$user->isAdminTong()) {
            $query->where('warehouse_id', $user->warehouse_id);
        } elseif ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('id_card', 'like', '%' . $request->search . '%');
            });
        }

        $staffs = $query->paginate(15)->appends($request->query());
        $warehouses = $user->isAdminTong() ? Warehouse::where('status', 'active')->get() : collect();

        return view('warehouse_staffs.index', compact('staffs', 'warehouses'));
    }

    public function create()
    {
        $user = Auth::user();
        $warehouses = $user->isAdminTong()
            ? Warehouse::where('status', 'active')->get()
            : Warehouse::where('id', $user->warehouse_id)->get();

        $users = User::whereIn('role', ['Admin kho', 'Nhân viên kho'])
                     ->orderBy('name')->get();

        return view('warehouse_staffs.create', compact('warehouses', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'  => 'required|exists:warehouses,id',
            'full_name'     => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'id_card'       => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'gender'        => 'required|in:male,female,other',
            'position'      => 'nullable|string|max:100',
            'start_date'    => 'nullable|date',
            'base_salary'   => 'required|numeric|min:0',
            'status'        => 'required|in:active,inactive',
            'user_id'       => 'nullable|exists:users,id',
            'note'          => 'nullable|string',
        ]);

        WarehouseStaff::create($validated);
        return redirect()->route('warehouse-staffs.index')
                         ->with('success', 'Thêm nhân viên kho thành công!');
    }

    public function show(WarehouseStaff $warehouseStaff)
    {
        $warehouseStaff->load(['warehouse', 'user', 'shiftLogs.shift', 'salaryRecords']);

        // Lấy thống kê 3 tháng gần nhất
        $recentSalaries = $warehouseStaff->salaryRecords()
            ->orderByDesc('year')->orderByDesc('month')
            ->take(3)->get();

        return view('warehouse_staffs.show', compact('warehouseStaff', 'recentSalaries'));
    }

    public function edit(WarehouseStaff $warehouseStaff)
    {
        $user = Auth::user();
        $warehouses = $user->isAdminTong()
            ? Warehouse::where('status', 'active')->get()
            : Warehouse::where('id', $user->warehouse_id)->get();

        $users = User::whereIn('role', ['Admin kho', 'Nhân viên kho'])
                     ->orderBy('name')->get();

        return view('warehouse_staffs.edit', compact('warehouseStaff', 'warehouses', 'users'));
    }

    public function update(Request $request, WarehouseStaff $warehouseStaff)
    {
        $validated = $request->validate([
            'warehouse_id'  => 'required|exists:warehouses,id',
            'full_name'     => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'id_card'       => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'gender'        => 'required|in:male,female,other',
            'position'      => 'nullable|string|max:100',
            'start_date'    => 'nullable|date',
            'base_salary'   => 'required|numeric|min:0',
            'status'        => 'required|in:active,inactive',
            'user_id'       => 'nullable|exists:users,id',
            'note'          => 'nullable|string',
        ]);

        $warehouseStaff->update($validated);
        return redirect()->route('warehouse-staffs.show', $warehouseStaff)
                         ->with('success', 'Cập nhật thông tin nhân viên thành công!');
    }

    public function destroy(WarehouseStaff $warehouseStaff)
    {
        // Kiểm tra có bảng lương chưa thanh toán không
        $unpaid = $warehouseStaff->salaryRecords()
                       ->whereIn('status', ['draft', 'confirmed'])->count();
        if ($unpaid > 0) {
            return back()->with('error', 'Không thể xóa: Nhân viên còn bảng lương chưa thanh toán.');
        }

        $warehouseStaff->delete();
        return redirect()->route('warehouse-staffs.index')
                         ->with('success', 'Đã xóa nhân viên kho.');
    }
}
