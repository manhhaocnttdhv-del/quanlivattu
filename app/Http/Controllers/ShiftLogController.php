<?php

namespace App\Http\Controllers;

use App\Models\ShiftLog;
use App\Models\Shift;
use App\Models\WarehouseStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Lấy danh sách nhân viên có quyền xem
        $staffQuery = WarehouseStaff::query();
        if ($user->isNhanVienKho()) {
            $staffQuery->where('user_id', $user->id);
        } elseif (!$user->isAdminTong()) {
            $staffQuery->where('warehouse_id', $user->warehouse_id);
        } elseif ($request->filled('warehouse_id')) {
            $staffQuery->where('warehouse_id', $request->warehouse_id);
        }

        $staffIds = $staffQuery->pluck('id');
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $logs = ShiftLog::with(['staff.warehouse', 'shift'])
            ->whereIn('staff_id', $staffIds)
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year)
            ->orderBy('work_date', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $warehouses = $user->isAdminTong()
            ? \App\Models\Warehouse::where('status', 'active')->get()
            : collect();

        return view('shift_logs.index', compact('logs', 'warehouses', 'month', 'year'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $staffs = WarehouseStaff::where('status', 'active')
            ->when($user->isNhanVienKho(), fn($q) => $q->where('user_id', $user->id))
            ->when(!$user->isAdminTong() && !$user->isNhanVienKho(), fn($q) => $q->where('warehouse_id', $user->warehouse_id))
            ->orderBy('full_name')->get();

        $shifts = Shift::query()
            ->when(!$user->isAdminTong(), fn($q) => $q->where('warehouse_id', $user->warehouse_id))
            ->orderBy('name')->get();

        return view('shift_logs.create', compact('staffs', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id'  => 'required|exists:warehouse_staffs,id',
            'shift_id'  => 'required|exists:shifts,id',
            'work_date' => 'required|date',
            'check_in'  => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status'    => 'required|in:present,absent,late,half_day',
            'note'      => 'nullable|string',
        ]);
        if (Auth::user()->isNhanVienKho()) {
            $myStaffId = WarehouseStaff::where('user_id', Auth::id())->value('id');
            if ($validated['staff_id'] != $myStaffId) {
                abort(403, 'Bạn chỉ được phép chấm công cho chính mình.');
            }
        }

        ShiftLog::updateOrCreate(
            ['staff_id' => $validated['staff_id'], 'shift_id' => $validated['shift_id'], 'work_date' => $validated['work_date']],
            $validated
        );

        return redirect()->route('shift-logs.index')
                         ->with('success', 'Đã lưu chấm công thành công!');
    }

    /**
     * Chấm công hàng loạt cho cả nhóm trong 1 ngày
     */
    public function bulkStore(Request $request)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không có quyền truy cập chức năng này.');


        $validated = $request->validate([
            'work_date'    => 'required|date',
            'shift_id'     => 'required|exists:shifts,id',
            'logs'         => 'required|array',
            'logs.*.staff_id' => 'required|exists:warehouse_staffs,id',
            'logs.*.status'   => 'required|in:present,absent,late,half_day',
            'logs.*.check_in'  => 'nullable|date_format:H:i',
            'logs.*.check_out' => 'nullable|date_format:H:i',
            'logs.*.note'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['logs'] as $log) {
                ShiftLog::updateOrCreate(
                    ['staff_id' => $log['staff_id'], 'shift_id' => $validated['shift_id'], 'work_date' => $validated['work_date']],
                    array_merge($log, ['shift_id' => $validated['shift_id'], 'work_date' => $validated['work_date']])
                );
            }
        });

        return redirect()->route('shift-logs.index')
                         ->with('success', 'Đã chấm công hàng loạt thành công!');
    }

    public function bulkCreate(Request $request)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không có quyền truy cập chức năng này.');

        $user = Auth::user();

        $staffs = WarehouseStaff::where('status', 'active')
            ->when(!$user->isAdminTong(), fn($q) => $q->where('warehouse_id', $user->warehouse_id))
            ->orderBy('full_name')->get();

        $shifts = Shift::query()
            ->when(!$user->isAdminTong(), fn($q) => $q->where('warehouse_id', $user->warehouse_id))
            ->orderBy('name')->get();

        return view('shift_logs.bulk_create', compact('staffs', 'shifts'));
    }

    public function destroy(ShiftLog $shiftLog)
    {
        abort_if(Auth::user()->isNhanVienKho(), 403, 'Nhân viên không được phép xóa.');

        $shiftLog->delete();
        return back()->with('success', 'Đã xóa bản ghi chấm công.');
    }
}
