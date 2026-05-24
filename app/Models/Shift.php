<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'name', 'start_time', 'end_time', 'note'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class);
    }

    public function getDurationAttribute(): string
    {
        return $this->start_time . ' - ' . $this->end_time;
    }
}
