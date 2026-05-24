<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Models\UserPermission;
use App\Models\User;

class PermissionController extends Controller
{
    private array $roles = ['Admin tổng', 'Admin kho', 'Nhân viên kho'];

    /* ──────────────────────────────────────────────────
     | Phân quyền theo ROLE
     |────────────────────────────────────────────────── */

    /** Hiển thị ma trận phân quyền (có thể chỉnh sửa) */
    public function index()
    {
        // Load từ DB
        $dbPerms = RolePermission::all();
        $rawGroups = config('permissions');

        $permissions = collect($rawGroups)->map(function ($group) use ($dbPerms) {
            $rows = collect($group['permissions'])->map(function ($perm) use ($group, $dbPerms) {
                $byRole = [];
                foreach ($this->roles as $role) {
                    // Tìm trong DB
                    $dbRecord = $dbPerms->where('role', $role)->where('permission_name', $perm['name'])->first();

                    if ($dbRecord) {
                        $byRole[$role] = (bool) $dbRecord->is_granted;
                    } else {
                        // Fallback mặc định từ config nếu DB chưa có
                        $byRole[$role] = in_array($role, $perm['roles']);
                    }
                }
                return [
                    'name'        => $perm['name'],
                    'description' => $perm['description'] ?? '',
                    'by_role'     => $byRole,
                ];
            });
            return ['group' => $group['group'], 'permissions' => $rows];
        });

        return view('permissions.index', [
            'permissions' => $permissions,
            'roles'       => $this->roles,
        ]);
    }

    /** Lưu thay đổi ma trận phân quyền theo role */
    public function update(Request $request)
    {
        $groups     = config('permissions');
        $submitted  = $request->input('perms', []); // perms[role][perm_name] = 1

        foreach ($groups as $group) {
            foreach ($group['permissions'] as $perm) {
                foreach ($this->roles as $role) {
                    $isGranted = isset($submitted[$role][$perm['name']]);

                    RolePermission::updateOrCreate(
                        ['role' => $role, 'permission_name' => $perm['name']],
                        [
                            'group_name'  => $group['group'],
                            'description' => $perm['description'] ?? null,
                            'is_granted'  => $isGranted,
                        ]
                    );
                }
            }
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Đã lưu cấu hình phân quyền thành công!');
    }

}
