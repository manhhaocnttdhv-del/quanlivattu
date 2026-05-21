<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialWarehouse extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'material_id', 'stock', 'location', 'average_cost', 'cost_price', 'selling_price'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
