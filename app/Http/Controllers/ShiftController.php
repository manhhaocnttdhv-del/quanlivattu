<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftLog;
use App\Models\WarehouseStaff;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Shift::with('warehouse')->latest();

        if (!$user->isAdminTong()) {
            $query->where('warehouse_id', $user->warehouse_id);
        } elseif ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $shifts = $query->paginate(15)->appends($request->query());
        $warehouses = $user->isAdminTong() ? Warehouse::where('status', 'active')->get() : collect();

        return view('shifts.index', compact('shifts', 'warehouses'));
    }

    public function create()
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không được phép tạo ca.');

        $user = Auth::user();
        $warehouses = $user->isAdminTong()
            ? Warehouse::where('status', 'active')->get()
            : Warehouse::where('id', $user->warehouse_id)->get();

        return view('shifts.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không được phép tạo ca.');

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name'         => 'required|string|max:100',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i',
            'note'         => 'nullable|string',
        ]);

        Shift::create($validated);
        return redirect()->route('shifts.index')->with('success', 'Thêm ca làm việc thành công!');
    }

    public function edit(Shift $shift)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không được phép sửa ca.');

        $user = Auth::user();
        $warehouses = $user->isAdminTong()
            ? Warehouse::where('status', 'active')->get()
            : Warehouse::where('id', $user->warehouse_id)->get();

        return view('shifts.edit', compact('shift', 'warehouses'));
    }

    public function update(Request $request, Shift $shift)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không được phép sửa ca.');

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name'         => 'required|string|max:100',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i',
            'note'         => 'nullable|string',
        ]);

        $shift->update($validated);
        return redirect()->route('shifts.index')->with('success', 'Cập nhật ca làm việc thành công!');
    }

    public function destroy(Shift $shift)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không được phép xóa ca.');

        if ($shift->shiftLogs()->count() > 0) {
            return back()->with('error', 'Không thể xóa ca đã có dữ liệu chấm công.');
        }
        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Đã xóa ca làm việc.');
    }
}
