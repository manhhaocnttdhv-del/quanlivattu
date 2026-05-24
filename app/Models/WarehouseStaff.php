<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStaff extends Model
{
    use HasFactory;

    protected $table = 'warehouse_staffs';

    protected $fillable = [
        'user_id', 'warehouse_id', 'full_name', 'phone', 'id_card',
        'address', 'date_of_birth', 'gender', 'position',
        'start_date', 'base_salary', 'status', 'note',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'start_date'    => 'date',
        'base_salary'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class, 'staff_id');
    }

    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class, 'staff_id');
    }

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'male'   => 'Nam',
            'female' => 'Nữ',
            default  => 'Khác',
        };
    }
}
