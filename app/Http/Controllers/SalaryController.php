<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\WarehouseStaff;
use App\Models\ShiftLog;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $query = SalaryRecord::with(['staff.warehouse'])
            ->where('month', $month)->where('year', $year);

        if (!$user->isAdminTong()) {
            $staffIds = WarehouseStaff::where('warehouse_id', $user->warehouse_id)->pluck('id');
            $query->whereIn('staff_id', $staffIds);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->paginate(20)->appends($request->query());

        $warehouses = $user->isAdminTong()
            ? \App\Models\Warehouse::where('status', 'active')->get()
            : collect();

        return view('salaries.index', compact('records', 'month', 'year', 'warehouses'));
    }

    /**
     * Tự động tính lương cho tất cả nhân viên trong tháng/năm
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month'        => 'required|integer|min:1|max:12',
            'year'         => 'required|integer|min:2020|max:2099',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $user = Auth::user();
        $standardDays = (int) AppSetting::get('standard_work_days', 26);

        $staffQuery = WarehouseStaff::where('status', 'active');
        if (!$user->isAdminTong()) {
            $staffQuery->where('warehouse_id', $user->warehouse_id);
        } elseif (!empty($validated['warehouse_id'])) {
            $staffQuery->where('warehouse_id', $validated['warehouse_id']);
        }

        $staffs = $staffQuery->get();
        $count  = 0;

        DB::transaction(function () use ($staffs, $validated, $standardDays, &$count) {
            foreach ($staffs as $staff) {
                // Tính tổng ngày công = sum hệ số (present/late = 1, half_day = 0.5, absent = 0)
                $logs = ShiftLog::where('staff_id', $staff->id)
                    ->whereMonth('work_date', $validated['month'])
                    ->whereYear('work_date', $validated['year'])
                    ->get();

                $totalDays = $logs->sum(fn($l) => match($l->status) {
                    'present', 'late' => 1.0,
                    'half_day'        => 0.5,
                    default           => 0.0,
                });

                $actualSalary = $standardDays > 0
                    ? round($staff->base_salary * $totalDays / $standardDays, 0)
                    : 0;

                // updateOrCreate — không ghi đè bonus/deduction nếu đã confirmed/paid
                $existing = SalaryRecord::where('staff_id', $staff->id)
                    ->where('month', $validated['month'])
                    ->where('year', $validated['year'])->first();

                if ($existing && in_array($existing->status, ['confirmed', 'paid'])) {
                    continue; // Bỏ qua bản ghi đã duyệt
                }

                $bonus     = $existing ? $existing->bonus     : 0;
                $deduction = $existing ? $existing->deduction : 0;
                $finalSalary = $actualSalary + $bonus - $deduction;

                SalaryRecord::updateOrCreate(
                    ['staff_id' => $staff->id, 'month' => $validated['month'], 'year' => $validated['year']],
                    [
                        'standard_work_days' => $standardDays,
                        'total_work_days'    => $totalDays,
                        'base_salary'        => $staff->base_salary,
                        'actual_salary'      => $actualSalary,
                        'bonus'              => $bonus,
                        'deduction'          => $deduction,
                        'final_salary'       => $finalSalary,
                        'status'             => 'draft',
                    ]
                );
                $count++;
            }
        });

        return redirect()->route('salaries.index', ['month' => $validated['month'], 'year' => $validated['year']])
                         ->with('success', "Đã tính lương cho {$count} nhân viên tháng {$validated['month']}/{$validated['year']}!");
    }

    public function show(SalaryRecord $salary)
    {
        $salary->load(['staff.warehouse', 'staff.shiftLogs' => function ($q) use ($salary) {
            $q->whereMonth('work_date', $salary->month)
              ->whereYear('work_date', $salary->year)
              ->with('shift')->orderBy('work_date');
        }]);
        return view('salaries.show', compact('salary'));
    }

    public function update(Request $request, SalaryRecord $salary)
    {
        if ($salary->status === 'paid') {
            return back()->with('error', 'Không thể chỉnh sửa bảng lương đã thanh toán.');
        }

        $validated = $request->validate([
            'bonus'     => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'note'      => 'nullable|string',
        ]);

        $finalSalary = $salary->actual_salary + $validated['bonus'] - $validated['deduction'];
        $salary->update(array_merge($validated, ['final_salary' => $finalSalary]));

        return back()->with('success', 'Đã cập nhật bảng lương!');
    }

    public function confirm(SalaryRecord $salary)
    {
        if ($salary->status !== 'draft') {
            return back()->with('error', 'Chỉ có thể xác nhận bảng lương ở trạng thái Nháp.');
        }
        $salary->update(['status' => 'confirmed']);
        return back()->with('success', 'Đã xác nhận bảng lương!');
    }

    public function pay(SalaryRecord $salary)
    {
        if ($salary->status !== 'confirmed') {
            return back()->with('error', 'Chỉ thanh toán được bảng lương đã xác nhận.');
        }
        $salary->update(['status' => 'paid']);
        return back()->with('success', 'Đã đánh dấu đã thanh toán lương!');
    }
}
