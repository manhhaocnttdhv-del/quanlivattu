<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransferDetail extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_transfer_id', 'material_id', 'quantity'];

    public function inventoryTransfer()
    {
        return $this->belongsTo(InventoryTransfer::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
