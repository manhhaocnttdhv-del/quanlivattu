<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit_id', 'description', 'min_stock', 'max_stock'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'material_warehouses')
                    ->withPivot('stock', 'location', 'average_cost')
                    ->withTimestamps();
    }

    public function warehouseStocks()
    {
        return $this->hasMany(MaterialWarehouse::class);
    }
}
