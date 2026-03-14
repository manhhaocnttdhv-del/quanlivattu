<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCheckDetail extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_check_id', 'material_id', 'system_stock', 'actual_stock', 'variance'];

    public function inventoryCheck()
    {
        return $this->belongsTo(InventoryCheck::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
