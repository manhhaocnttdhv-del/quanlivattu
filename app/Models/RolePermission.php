<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role',
        'permission_name',
        'group_name',
        'description',
        'is_granted',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
    ];
}
