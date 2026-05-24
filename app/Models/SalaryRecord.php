<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'month', 'year',
        'standard_work_days', 'total_work_days',
        'base_salary', 'actual_salary',
        'bonus', 'deduction', 'final_salary',
        'status', 'note',
    ];

    protected $casts = [
        'base_salary'   => 'decimal:2',
        'actual_salary' => 'decimal:2',
        'bonus'         => 'decimal:2',
        'deduction'     => 'decimal:2',
        'final_salary'  => 'decimal:2',
        'total_work_days' => 'decimal:1',
    ];

    public function staff()
    {
        return $this->belongsTo(WarehouseStaff::class, 'staff_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Nháp',
            'confirmed' => 'Đã xác nhận',
            'paid'      => 'Đã thanh toán',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'secondary',
            'confirmed' => 'warning',
            'paid'      => 'success',
            default     => 'secondary',
        };
    }

    public function getPeriodAttribute(): string
    {
        return 'Tháng ' . $this->month . '/' . $this->year;
    }
}
