<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'shift_id', 'work_date',
        'check_in', 'check_out', 'status', 'note',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(WarehouseStaff::class, 'staff_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'present'  => 'Có mặt',
            'absent'   => 'Vắng mặt',
            'late'     => 'Đi trễ',
            'half_day' => 'Nửa ngày',
            default    => $this->status,
        };
    }

    /** Hệ số ngày công: present=1, late=1, half_day=0.5, absent=0 */
    public function getWorkDayFactorAttribute(): float
    {
        return match($this->status) {
            'present'  => 1.0,
            'late'     => 1.0,
            'half_day' => 0.5,
            'absent'   => 0.0,
            default    => 0.0,
        };
    }
}
