<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'warehouse_id',
        'batch_code',
        'expiry_date',
        'stock',
    ];
}
