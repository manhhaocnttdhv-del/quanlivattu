<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'license_plate',
        'driver_name',
        'driver_phone',
        'contact_name',
        'contact_phone',
        'status',
        'note'
    ];

    public function inventoryEntries()
    {
        return $this->hasMany(InventoryEntry::class);
    }

    public function inventoryExits()
    {
        return $this->hasMany(InventoryExit::class);
    }

    public function inventoryTransfers()
    {
        return $this->hasMany(InventoryTransfer::class);
    }
}
