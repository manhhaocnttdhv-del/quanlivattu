<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryExitDetail extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_exit_id', 'material_id', 'quantity', 'unit_price', 'location'];

    public function inventoryExit()
    {
        return $this->belongsTo(InventoryExit::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
