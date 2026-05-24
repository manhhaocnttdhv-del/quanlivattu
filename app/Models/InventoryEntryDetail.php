<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryEntryDetail extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_entry_id', 'material_id', 'quantity', 'unit_price', 'location', 'status'];

    public function inventoryEntry()
    {
        return $this->belongsTo(InventoryEntry::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
