<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ma trận phân quyền theo Role
    |--------------------------------------------------------------------------
    | Mỗi nhóm gồm:
    |   'group'       => Tên nhóm quyền
    |   'permissions' => Danh sách quyền, mỗi quyền:
    |       'name'        => Tên quyền
    |       'description' => Mô tả ngắn
    |       'roles'       => Các role ĐƯỢC PHÉP (Admin tổng / Admin kho / Nhân viên kho)
    */

    [
        'group' => 'Quản lý Danh mục',
        'permissions' => [
            [
                'name'        => 'Xem danh sách vật tư',
                'description' => 'Truy cập trang danh sách vật tư',
                'roles'       => ['Admin tổng', 'Admin kho', 'Nhân viên kho'],
            ],
            [
                'name'        => 'Thêm / Sửa / Xóa vật tư',
                'description' => 'Tạo mới, cập nhật, xóa vật tư',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Quản lý đơn vị tính',
                'description' => 'Thêm, sửa, xóa đơn vị tính',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Quản lý nhà cung cấp',
                'description' => 'Thêm, sửa, xóa nhà cung cấp',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Quản lý khách hàng',
                'description' => 'Thêm, sửa, xóa khách hàng',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Quản lý Kho',
        'permissions' => [
            [
                'name'        => 'Xem danh sách kho',
                'description' => 'Truy cập trang quản lý kho hàng',
                'roles'       => ['Admin tổng'],
            ],
            [
                'name'        => 'Thêm / Sửa / Xóa kho',
                'description' => 'Tạo mới, cập nhật, xóa kho hàng',
                'roles'       => ['Admin tổng'],
            ],
        ],
    ],

    [
        'group' => 'Nghiệp vụ Nhập kho',
        'permissions' => [
            [
                'name'        => 'Tạo phiếu nhập kho',
                'description' => 'Lập phiếu nhập kho mới',
                'roles'       => ['Admin tổng', 'Admin kho', 'Nhân viên kho'],
            ],
            [
                'name'        => 'Duyệt / Hủy phiếu nhập kho',
                'description' => 'Phê duyệt hoặc hủy phiếu nhập kho',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Xuất Excel / PDF nhập kho',
                'description' => 'Xuất danh sách phiếu nhập kho ra file',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Nghiệp vụ Xuất kho',
        'permissions' => [
            [
                'name'        => 'Tạo phiếu xuất kho',
                'description' => 'Lập phiếu xuất kho mới',
                'roles'       => ['Admin tổng', 'Admin kho', 'Nhân viên kho'],
            ],
            [
                'name'        => 'Duyệt / Hủy phiếu xuất kho',
                'description' => 'Phê duyệt hoặc hủy phiếu xuất kho',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Xuất Excel / PDF xuất kho',
                'description' => 'Xuất danh sách phiếu xuất kho ra file',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Nghiệp vụ Chuyển kho',
        'permissions' => [
            [
                'name'        => 'Tạo phiếu chuyển kho',
                'description' => 'Lập phiếu chuyển hàng giữa các kho',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Duyệt / Hủy phiếu chuyển kho',
                'description' => 'Phê duyệt hoặc hủy phiếu chuyển kho',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Kiểm kê kho',
        'permissions' => [
            [
                'name'        => 'Tạo phiếu kiểm kê',
                'description' => 'Lập phiếu kiểm kê kho',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Duyệt / Hủy phiếu kiểm kê',
                'description' => 'Phê duyệt hoặc hủy phiếu kiểm kê',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Báo cáo',
        'permissions' => [
            [
                'name'        => 'Xem báo cáo tồn kho',
                'description' => 'Truy cập trang báo cáo tồn kho',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Xuất báo cáo Excel / PDF',
                'description' => 'Xuất báo cáo tồn kho ra file',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Cảnh báo Tồn kho',
        'permissions' => [
            [
                'name'        => 'Xem cảnh báo tồn kho',
                'description' => 'Xem danh sách vật tư dưới mức tồn kho tối thiểu',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Xử lý cảnh báo tồn kho',
                'description' => 'Đánh dấu đã xử lý cảnh báo',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

    [
        'group' => 'Quản lý Người dùng',
        'permissions' => [
            [
                'name'        => 'Phân quyền người dùng',
                'description' => 'Xem ma trận phân quyền theo role',
                'roles'       => ['Admin tổng'],
            ],
        ],
    ],

    [
        'group' => 'Quản lý Nhân viên Kho',
        'permissions' => [
            [
                'name'        => 'Xem nhân viên kho',
                'description' => 'Xem danh sách nhân viên, hồ sơ nhân viên của kho mình',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Thêm / Sửa / Xóa nhân viên kho',
                'description' => 'Tạo, cập nhật, xóa hồ sơ nhân viên',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
            [
                'name'        => 'Quản lý ca làm việc',
                'description' => 'Tạo ca, duyệt công (đối với Admin) / Tự chấm công (đối với Nhân viên)',
                'roles'       => ['Admin tổng', 'Admin kho', 'Nhân viên kho'],
            ],
            [
                'name'        => 'Quản lý lương',
                'description' => 'Tính lương, xác nhận và thanh toán bảng lương',
                'roles'       => ['Admin tổng', 'Admin kho'],
            ],
        ],
    ],

];
