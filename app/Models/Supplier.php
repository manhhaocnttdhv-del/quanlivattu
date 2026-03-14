<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'warehouse_id'];

    public function inventoryEntries()
    {
        return $this->hasMany(InventoryEntry::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
