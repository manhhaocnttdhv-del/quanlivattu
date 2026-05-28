<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'from_warehouse_id',
        'to_warehouse_id',
        'user_id',
        'status',
        'note',
        'delivery_partner_id',
        'delivery_status',
        'delivery_fee',
        'delivery_code'
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryPartner()
    {
        return $this->belongsTo(DeliveryPartner::class);
    }

    public function details()
    {
        return $this->hasMany(InventoryTransferDetail::class);
    }
}
