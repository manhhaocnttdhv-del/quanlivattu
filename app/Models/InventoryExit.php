<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'warehouse_id',
        'project_id',
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

    public function project()
    {
        return $this->belongsTo(Project::class);
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
        return $this->hasMany(InventoryExitDetail::class);
    }
}
