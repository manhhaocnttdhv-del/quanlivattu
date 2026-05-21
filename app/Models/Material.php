<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit_id', 'category_id', 'description', 'cost_price', 'selling_price', 'min_stock', 'max_stock'];

    protected $appends = ['profit', 'profit_margin'];

    /**
     * Lợi nhuận = Giá bán - Giá vốn
     */
    public function getProfitAttribute(): float
    {
        return $this->selling_price - $this->cost_price;
    }

    /**
     * Biên lợi nhuận (%) = (Giá bán - Giá vốn) / Giá vốn * 100
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) return 0;
        return round(($this->selling_price - $this->cost_price) / $this->cost_price * 100, 1);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
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

