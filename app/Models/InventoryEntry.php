<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'warehouse_id',
        'supplier_id',
        'user_id',
        'status',
        'note',
        'delivery_partner_id',
        'delivery_status',
        'delivery_fee',
        'delivery_code'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
        return $this->hasMany(InventoryEntryDetail::class);
    }
}
