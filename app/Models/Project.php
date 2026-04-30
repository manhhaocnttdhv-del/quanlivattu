<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'warehouse_id'];

    public function inventoryExits()
    {
        return $this->hasMany(InventoryExit::class);
    }

    public function projectMaterials()
    {
        return $this->hasMany(ProjectMaterial::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
