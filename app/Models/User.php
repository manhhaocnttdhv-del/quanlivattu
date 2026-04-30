<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'warehouse_id',
        'status',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }



    /**
     * Kiểm tra user có thuộc một trong các role không.
     * @param string|array $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array(trim($this->role), $roles);
    }

    public function isAdminTong(): bool
    {
        return $this->role === 'Admin tổng';
    }

    public function isAdminKho(): bool
    {
        return $this->role === 'Admin kho';
    }

    public function isNhanVienKho(): bool
    {
        return $this->role === 'Nhân viên kho';
    }

    public function hasPermission(string $permissionName): bool
    {
        // Kiểm tra quyền theo role
        $rolePerm = RolePermission::where('role', $this->role)
            ->where('permission_name', $permissionName)
            ->first();

        return $rolePerm ? (bool) $rolePerm->is_granted : false;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
