<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryExit extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'warehouse_id', 'customer_id', 'user_id', 'status'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(InventoryExitDetail::class);
    }
}
