<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'manager_id', 'status'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'material_warehouses')
                    ->withPivot('stock')
                    ->withTimestamps();
    }
}
