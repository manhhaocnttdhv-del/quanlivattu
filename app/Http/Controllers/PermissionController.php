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
        if (!auth()->user()->isAdminTong()) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Load từ DB, nhóm theo group_name
        $dbPerms = RolePermission::all()->groupBy('group_name');

        // Nếu DB chưa có dữ liệu, fallback từ config
        if ($dbPerms->isEmpty()) {
            $rawGroups  = config('permissions');
            $permissions = collect($rawGroups)->map(function ($group) {
                $rows = collect($group['permissions'])->map(function ($perm) use ($group) {
                    $byRole = [];
                    foreach ($this->roles as $role) {
                        $byRole[$role] = in_array($role, $perm['roles']);
                    }
                    return [
                        'name'        => $perm['name'],
                        'description' => $perm['description'] ?? '',
                        'by_role'     => $byRole,
                    ];
                });
                return ['group' => $group['group'], 'permissions' => $rows];
            });
        } else {
            $permissions = $dbPerms->map(function ($perms, $groupName) {
                // pivot: permission_name → role → is_granted
                $byName = $perms->groupBy('permission_name');
                $rows = $byName->map(function ($roleRows, $permName) {
                    $byRole = [];
                    foreach ($this->roles as $role) {
                        $row = $roleRows->firstWhere('role', $role);
                        $byRole[$role] = $row ? (bool) $row->is_granted : false;
                    }
                    $first = $roleRows->first();
                    return [
                        'name'        => $permName,
                        'description' => $first->description ?? '',
                        'by_role'     => $byRole,
                    ];
                })->values();

                return ['group' => $groupName, 'permissions' => $rows];
            })->values();
        }

        return view('permissions.index', [
            'permissions' => $permissions,
            'roles'       => $this->roles,
        ]);
    }

    /** Lưu thay đổi ma trận phân quyền theo role */
    public function update(Request $request)
    {
        if (!auth()->user()->isAdminTong()) {
            abort(403);
        }

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
