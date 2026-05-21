# TÀI LIỆU NGHIỆP VỤ HỆ THỐNG QUẢN LÝ VẬT TƯ

## Mục lục

1. [Tổng quan hệ thống](#1-tổng-quan-hệ-thống)
2. [Hệ thống phân quyền (Roles & Permissions)](#2-hệ-thống-phân-quyền)
3. [Quản lý Danh mục](#3-quản-lý-danh-mục)
4. [Quản lý Kho hàng](#4-quản-lý-kho-hàng)
5. [Nghiệp vụ Nhập kho](#5-nghiệp-vụ-nhập-kho)
6. [Nghiệp vụ Xuất kho](#6-nghiệp-vụ-xuất-kho)
7. [Nghiệp vụ Chuyển kho](#7-nghiệp-vụ-chuyển-kho)
8. [Nghiệp vụ Kiểm kê kho](#8-nghiệp-vụ-kiểm-kê-kho)
9. [Hệ thống Cảnh báo tồn kho](#9-hệ-thống-cảnh-báo-tồn-kho)
10. [Báo cáo & Xuất dữ liệu](#10-báo-cáo--xuất-dữ-liệu)
11. [Quản lý Người dùng](#11-quản-lý-người-dùng)
12. [Dashboard & Thống kê](#12-dashboard--thống-kê)
13. [Sơ đồ luồng nghiệp vụ](#13-sơ-đồ-luồng-nghiệp-vụ)
14. [Ma trận phân quyền chi tiết](#14-ma-trận-phân-quyền-chi-tiết)

---

## 1. Tổng quan hệ thống

### 1.1. Mô tả chung

Hệ thống **Quản lý Vật tư** là ứng dụng web được xây dựng trên nền tảng **Laravel** (PHP), phục vụ quản lý toàn bộ quy trình nhập - xuất - chuyển - kiểm kê vật tư trong hệ thống kho hàng đa chi nhánh. Hệ thống hỗ trợ:

- Quản lý nhiều kho hàng đồng thời
- Theo dõi tồn kho theo thời gian thực (real-time)
- Tính giá vốn bình quân gia quyền (Weighted Average Cost)
- Kiểm soát định mức xuất kho theo dự toán công trình (BoQ - Bill of Quantities)
- Cảnh báo tồn kho tự động khi dưới mức tối thiểu
- Quy trình phê duyệt phiếu (Approval Workflow)
- Phân quyền chi tiết theo vai trò (Role-Based Access Control)

### 1.2. Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel (PHP) |
| Database | MySQL |
| Authentication | Laravel Auth (Session-based) |
| Export Excel | Maatwebsite/Excel |
| Export PDF | Barryvdh/DomPDF |
| API Token | Laravel Sanctum |

### 1.3. Kiến trúc dữ liệu chính

```
Users ──────────── Warehouses
  │                    │
  │                    ├── MaterialWarehouse (tồn kho theo kho)
  │                    │        │
  │                    │        └── Materials ── Units
  │                    │
  │                    ├── InventoryEntries ── InventoryEntryDetails
  │                    │        └── Suppliers
  │                    │
  │                    ├── InventoryExits ── InventoryExitDetails
  │                    │        └── Projects ── ProjectMaterials (Dự toán)
  │                    │
  │                    ├── InventoryTransfers ── InventoryTransferDetails
  │                    │
  │                    └── InventoryChecks ── InventoryCheckDetails
  │
  └── RolePermissions (Phân quyền)
```

---

## 2. Hệ thống phân quyền

### 2.1. Các vai trò (Roles)

Hệ thống có **3 vai trò** chính:

| Vai trò | Mã role | Mô tả |
|---|---|---|
| **Admin tổng** | `Admin tổng` | Quản trị viên cấp cao nhất, toàn quyền trên toàn hệ thống, quản lý tất cả các kho |
| **Admin kho** | `Admin kho` | Quản lý kho, phụ trách 1 kho cụ thể, có quyền duyệt phiếu và quản lý danh mục |
| **Nhân viên kho** | `Nhân viên kho` | Nhân viên thực hiện nghiệp vụ nhập/xuất, chỉ thao tác trên kho được gán |

### 2.2. Phạm vi dữ liệu theo Role

| Tiêu chí | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem dữ liệu | Tất cả các kho | Chỉ kho được gán | Chỉ kho được gán |
| Tạo phiếu | Chọn bất kỳ kho nào | Chỉ kho của mình | Chỉ kho của mình |
| Duyệt/Hủy phiếu | ✅ | ✅ | ❌ |
| Quản lý danh mục | ✅ | ✅ | ❌ (chỉ xem) |
| Quản lý kho | ✅ | ❌ (chỉ xem) | ❌ (chỉ xem) |
| Quản lý người dùng | ✅ | ❌ | ❌ |
| Phân quyền | ✅ | ❌ | ❌ |
| Cảnh báo tồn kho | ✅ | ✅ | ❌ |
| Báo cáo | ✅ (tất cả kho) | ✅ (kho của mình) | ✅ (kho của mình) |

### 2.3. Cơ chế phân quyền

Hệ thống sử dụng **2 lớp phân quyền**:

#### Lớp 1: Middleware `CheckRole`
- Kiểm tra role của user trước khi cho phép truy cập route
- Được áp dụng trực tiếp trên route groups
- Nếu user không thuộc role cho phép → trả về HTTP 403

#### Lớp 2: Bảng `RolePermission` (Phân quyền động)
- Admin tổng có thể cấu hình ma trận phân quyền chi tiết
- Mỗi permission gồm: `role`, `permission_name`, `group_name`, `description`, `is_granted`
- Kiểm tra qua method `$user->hasPermission('tên_quyền')`

---

## 3. Quản lý Danh mục

### 3.1. Quản lý Vật tư (Materials)

#### Mô tả
Danh mục vật tư là trung tâm của hệ thống, chứa thông tin về tất cả các loại vật tư được quản lý.

#### Thuộc tính vật tư
| Trường | Mô tả | Bắt buộc |
|---|---|---|
| `name` | Tên vật tư | ✅ |
| `unit_id` | Đơn vị tính (liên kết bảng Units) | ✅ |
| `description` | Mô tả chi tiết | ❌ |
| `min_stock` | Mức tồn kho tối thiểu (cảnh báo) | ❌ |
| `max_stock` | Mức tồn kho tối đa | ❌ |

#### Quyền thao tác
| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách | ✅ | ✅ | ✅ |
| Xem chi tiết | ✅ | ✅ | ✅ |
| Thêm mới | ✅ | ✅ | ❌ |
| Sửa | ✅ | ✅ | ❌ |
| Xóa | ✅ | ✅ | ❌ |
| Import Excel | ✅ | ✅ | ❌ |
| Export Excel | ✅ | ✅ | ❌ |

#### Luồng Import/Export
- **Export**: Xuất toàn bộ danh sách vật tư ra file `.xlsx`
- **Import**: Upload file `.xlsx/.xls/.csv` (tối đa 2MB) để nhập hàng loạt vật tư

### 3.2. Quản lý Đơn vị tính (Units)

#### Mô tả
Danh mục đơn vị tính (kg, m, cái, bộ, thùng...) dùng để gán cho vật tư.

#### Thuộc tính
| Trường | Mô tả | Bắt buộc |
|---|---|---|
| `name` | Tên đơn vị tính (unique) | ✅ |

#### Quyền thao tác
| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem | ✅ | ✅ | ✅ |
| Thêm/Sửa/Xóa | ✅ | ✅ | ❌ |

#### Ràng buộc
- Không thể xóa đơn vị tính đang được sử dụng bởi vật tư nào

### 3.3. Quản lý Nhà cung cấp (Suppliers)

#### Mô tả
Danh mục nhà cung cấp vật tư, liên kết với phiếu nhập kho.

#### Thuộc tính
| Trường | Mô tả | Bắt buộc |
|---|---|---|
| `name` | Tên nhà cung cấp (unique) | ✅ |
| `phone` | Số điện thoại | ❌ |
| `address` | Địa chỉ | ❌ |
| `warehouse_id` | Kho liên kết (phạm vi hiển thị) | ❌ |

#### Phạm vi dữ liệu
- **Admin tổng**: Xem tất cả nhà cung cấp, có thể gán cho bất kỳ kho nào
- **Admin kho / Nhân viên kho**: Chỉ xem nhà cung cấp thuộc kho mình hoặc không gán kho (warehouse_id = NULL)

#### Ràng buộc
- Không thể xóa nhà cung cấp đã có giao dịch nhập kho

### 3.4. Quản lý Công trình / Dự án (Projects)

#### Mô tả
Danh mục công trình/dự án, liên kết với phiếu xuất kho. Mỗi công trình có **Bảng định mức dự toán (BoQ)** quy định số lượng vật tư tối đa được phép xuất.

#### Thuộc tính
| Trường | Mô tả | Bắt buộc |
|---|---|---|
| `name` | Tên công trình (unique) | ✅ |
| `phone` | Số điện thoại liên hệ | ❌ |
| `address` | Địa chỉ công trình | ❌ |
| `warehouse_id` | Kho phụ trách | ❌ |

#### Bảng định mức dự toán (ProjectMaterials)
- Mỗi công trình có danh sách vật tư kèm **số lượng dự toán** (`estimated_quantity`)
- Khi xuất kho cho công trình, hệ thống kiểm tra:
  - Vật tư phải có trong dự toán
  - Tổng số lượng đã xuất + đang chờ xuất + yêu cầu mới ≤ Định mức dự toán

#### Quyền thao tác
| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem | ✅ | ✅ | ✅ |
| Thêm/Sửa/Xóa | ✅ | ✅ | ❌ |
| Cập nhật dự toán | ✅ | ✅ | ❌ |

#### Ràng buộc
- Không thể xóa công trình đã có phiếu xuất kho

---

## 4. Quản lý Kho hàng

### 4.1. Mô tả

Kho hàng là đơn vị lưu trữ vật tư vật lý. Hệ thống hỗ trợ quản lý đa kho, mỗi kho có người quản lý riêng.

### 4.2. Thuộc tính kho

| Trường | Mô tả | Bắt buộc |
|---|---|---|
| `name` | Tên kho (unique) | ✅ |
| `address` | Địa chỉ kho | ✅ |
| `manager_id` | Người quản lý (User có role Admin kho) | ❌ |
| `status` | Trạng thái: `active` / `inactive` | ✅ |

### 4.3. Quyền thao tác

| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách kho | ✅ | ✅ | ✅ |
| Xem chi tiết kho | ✅ | ✅ | ✅ |
| Thêm kho mới | ✅ | ❌ | ❌ |
| Sửa thông tin kho | ✅ | ❌ | ❌ |
| Xóa kho | ✅ | ❌ | ❌ |

### 4.4. Tồn kho theo kho (MaterialWarehouse)

Bảng trung gian `material_warehouses` lưu trữ thông tin tồn kho của từng vật tư tại từng kho:

| Trường | Mô tả |
|---|---|
| `warehouse_id` | ID kho |
| `material_id` | ID vật tư |
| `stock` | Số lượng tồn kho hiện tại |
| `location` | Vị trí kệ/ô trong kho |
| `average_cost` | Giá vốn bình quân gia quyền |

### 4.5. Công thức tính giá vốn bình quân

Khi nhập kho với đơn giá mới:

```
Giá vốn mới = (Tồn cũ × Giá vốn cũ + SL nhập × Đơn giá nhập) / (Tồn cũ + SL nhập)
```

---

## 5. Nghiệp vụ Nhập kho

### 5.1. Mô tả

Phiếu nhập kho ghi nhận việc nhập vật tư từ nhà cung cấp vào kho. Mỗi phiếu nhập có thể chứa nhiều dòng vật tư (chi tiết).

### 5.2. Thuộc tính phiếu nhập

| Trường | Mô tả |
|---|---|
| `date` | Ngày nhập |
| `warehouse_id` | Kho nhập |
| `supplier_id` | Nhà cung cấp |
| `user_id` | Người tạo phiếu |
| `status` | Trạng thái: `pending` / `completed` / `cancelled` |
| `note` | Ghi chú |

### 5.3. Chi tiết phiếu nhập (InventoryEntryDetail)

| Trường | Mô tả |
|---|---|
| `material_id` | Vật tư |
| `quantity` | Số lượng nhập |
| `price` | Đơn giá nhập |
| `location` | Vị trí lưu trữ trong kho |

### 5.4. Luồng nghiệp vụ (Workflow)

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────────┐
│  Tạo phiếu nhập │────▶│  Chờ duyệt       │────▶│  Đã duyệt           │
│  (pending)       │     │  (pending)       │     │  (completed)        │
└─────────────────┘     └──────────────────┘     └─────────────────────┘
                                │                          │
                                ▼                          ▼
                        ┌──────────────────┐     ┌─────────────────────┐
                        │  Hủy (từ pending)│     │  Hủy (từ completed) │
                        │  (cancelled)     │     │  (cancelled)        │
                        └──────────────────┘     │  → Hoàn trả tồn kho │
                                                 └─────────────────────┘
```

### 5.5. Chi tiết từng bước

#### Bước 1: Tạo phiếu nhập
- **Ai thực hiện**: Admin tổng, Admin kho, Nhân viên kho
- **Thao tác**:
  1. Chọn ngày nhập
  2. Chọn kho nhập (Admin tổng chọn bất kỳ, các role khác chỉ kho của mình)
  3. Chọn nhà cung cấp
  4. Thêm danh sách vật tư: chọn vật tư, nhập số lượng, đơn giá, vị trí
  5. Ghi chú (tùy chọn)
- **Kết quả**: Phiếu được tạo với trạng thái `pending` (chờ duyệt)
- **Lưu ý**: Tồn kho CHƯA thay đổi tại bước này

#### Bước 2: Duyệt phiếu nhập
- **Ai thực hiện**: Admin tổng, Admin kho
- **Điều kiện**: Phiếu phải ở trạng thái `pending`
- **Xử lý khi duyệt**:
  1. Cập nhật tồn kho: cộng số lượng vào `material_warehouses.stock`
  2. Tính lại giá vốn bình quân gia quyền
  3. Cập nhật vị trí lưu trữ (nếu có)
  4. Kiểm tra & tự động xử lý cảnh báo tồn kho
  5. Đổi trạng thái phiếu → `completed`

#### Bước 3 (tùy chọn): Hủy phiếu
- **Ai thực hiện**: Admin tổng, Admin kho
- **Trường hợp 1 - Hủy phiếu đang chờ**: Chỉ đổi trạng thái → `cancelled`, không ảnh hưởng tồn kho
- **Trường hợp 2 - Hủy phiếu đã duyệt**: Trừ lại số lượng đã cộng khỏi tồn kho, đổi trạng thái → `cancelled`

### 5.6. Quyền thao tác

| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách phiếu nhập | ✅ (tất cả kho) | ✅ (kho mình) | ✅ (kho mình) |
| Tạo phiếu nhập | ✅ | ✅ | ✅ |
| Xem chi tiết phiếu | ✅ | ✅ | ✅ |
| Duyệt phiếu | ✅ | ✅ | ❌ |
| Hủy phiếu | ✅ | ✅ | ❌ |
| Sửa phiếu | ❌ (không cho phép) | ❌ | ❌ |
| Xóa phiếu | ❌ (dùng Hủy thay thế) | ❌ | ❌ |
| Export Excel | ✅ | ✅ | ✅ |
| Export PDF | ✅ | ✅ | ✅ |

### 5.7. Quy tắc nghiệp vụ

- Phiếu nhập **không được phép sửa** sau khi tạo (immutable)
- Phiếu nhập **không được phép xóa vĩnh viễn**, chỉ có thể Hủy
- Khi duyệt, giá vốn bình quân được tính lại tự động
- Hệ thống tự động kiểm tra cảnh báo tồn kho sau mỗi lần cập nhật stock

---

## 6. Nghiệp vụ Xuất kho

### 6.1. Mô tả

Phiếu xuất kho ghi nhận việc xuất vật tư từ kho cho một công trình/dự án cụ thể. Hệ thống kiểm soát chặt chẽ theo **định mức dự toán (BoQ)** của công trình.

### 6.2. Thuộc tính phiếu xuất

| Trường | Mô tả |
|---|---|
| `date` | Ngày xuất |
| `warehouse_id` | Kho xuất |
| `project_id` | Công trình nhận vật tư |
| `user_id` | Người tạo phiếu |
| `status` | Trạng thái: `pending` / `completed` / `cancelled` |
| `note` | Ghi chú |

### 6.3. Chi tiết phiếu xuất (InventoryExitDetail)

| Trường | Mô tả |
|---|---|
| `material_id` | Vật tư |
| `quantity` | Số lượng xuất |
| `location` | Vị trí lấy hàng trong kho |

### 6.4. Luồng nghiệp vụ (Workflow)

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────────┐
│  Tạo phiếu xuất │────▶│  Chờ duyệt       │────▶│  Đã duyệt           │
│  (pending)       │     │  (pending)       │     │  (completed)        │
└─────────────────┘     └──────────────────┘     └─────────────────────┘
       │                        │                          │
       │ Kiểm tra:              ▼                          ▼
       │ - Tồn kho đủ?  ┌──────────────────┐     ┌─────────────────────┐
       │ - Định mức BoQ? │  Hủy (từ pending)│     │  Hủy (từ completed) │
       └─────────────────│  (cancelled)     │     │  (cancelled)        │
                         └──────────────────┘     │  → Hoàn trả tồn kho │
                                                  └─────────────────────┘
```

### 6.5. Chi tiết từng bước

#### Bước 1: Tạo phiếu xuất
- **Ai thực hiện**: Admin tổng, Admin kho, Nhân viên kho
- **Thao tác**:
  1. Chọn ngày xuất
  2. Chọn kho xuất
  3. Chọn công trình nhận
  4. Thêm danh sách vật tư: chọn vật tư, nhập số lượng, vị trí
- **Kiểm tra tự động khi tạo** (3 điều kiện):
  1. **Kiểm tra tồn kho**: Số lượng yêu cầu ≤ Tồn kho hiện tại tại kho xuất
  2. **Kiểm tra dự toán**: Vật tư phải có trong bảng định mức dự toán của công trình (estimated_quantity > 0)
  3. **Kiểm tra vượt định mức**: (Đã xuất + Đang chờ xuất + Yêu cầu mới) ≤ Định mức dự toán
- **Kết quả**: Nếu pass tất cả kiểm tra → Phiếu tạo với trạng thái `pending`
- **Lưu ý**: Tồn kho CHƯA thay đổi tại bước này

#### Bước 2: Duyệt phiếu xuất
- **Ai thực hiện**: Admin tổng, Admin kho
- **Điều kiện**: Phiếu phải ở trạng thái `pending`
- **Xử lý khi duyệt**:
  1. Trừ số lượng khỏi `material_warehouses.stock`
  2. Nếu tồn kho không đủ tại thời điểm duyệt → báo lỗi, không duyệt được
  3. Kiểm tra & tạo cảnh báo tồn kho nếu dưới mức tối thiểu
  4. Đổi trạng thái phiếu → `completed`

#### Bước 3 (tùy chọn): Hủy phiếu
- **Hủy phiếu đang chờ**: Chỉ đổi trạng thái, không ảnh hưởng tồn kho
- **Hủy phiếu đã duyệt**: Cộng lại số lượng đã trừ vào tồn kho

### 6.6. Quyền thao tác

| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách phiếu xuất | ✅ (tất cả kho) | ✅ (kho mình) | ✅ (kho mình) |
| Tạo phiếu xuất | ✅ | ✅ | ✅ |
| Xem chi tiết phiếu | ✅ | ✅ | ✅ |
| Duyệt phiếu | ✅ | ✅ | ❌ |
| Hủy phiếu | ✅ | ✅ | ❌ |
| Sửa phiếu | ❌ | ❌ | ❌ |
| Xóa phiếu | ❌ (dùng Hủy) | ❌ | ❌ |
| Export Excel | ✅ | ✅ | ✅ |
| Export PDF | ✅ | ✅ | ✅ |

### 6.7. Quy tắc nghiệp vụ đặc biệt

- **Kiểm soát định mức (BoQ)**: Đây là điểm khác biệt quan trọng so với nhập kho. Hệ thống KHÔNG cho phép xuất vượt định mức dự toán của công trình.
- **Tính toán đã xuất**: Bao gồm cả phiếu `completed` VÀ `pending` (đang chờ duyệt) để tránh trường hợp tạo nhiều phiếu chờ vượt định mức.
- Phiếu xuất **không được sửa** và **không được xóa vĩnh viễn**.

---

## 7. Nghiệp vụ Chuyển kho

### 7.1. Mô tả

Phiếu chuyển kho ghi nhận việc di chuyển vật tư từ kho này sang kho khác trong cùng hệ thống. Đây là nghiệp vụ nội bộ, không liên quan đến nhà cung cấp hay công trình.

### 7.2. Thuộc tính phiếu chuyển kho

| Trường | Mô tả |
|---|---|
| `date` | Ngày chuyển |
| `from_warehouse_id` | Kho nguồn (xuất đi) |
| `to_warehouse_id` | Kho đích (nhận vào) |
| `user_id` | Người tạo phiếu |
| `status` | Trạng thái: `pending` / `completed` / `cancelled` |
| `note` | Ghi chú |

### 7.3. Chi tiết phiếu chuyển (InventoryTransferDetail)

| Trường | Mô tả |
|---|---|
| `material_id` | Vật tư |
| `quantity` | Số lượng chuyển |
| `location` | Vị trí đặt tại kho đích |

### 7.4. Luồng nghiệp vụ

```
┌──────────────────┐     ┌──────────────────┐     ┌──────────────────────────┐
│  Tạo phiếu       │────▶│  Chờ duyệt       │────▶│  Đã duyệt                │
│  (pending)        │     │  (pending)       │     │  (completed)             │
└──────────────────┘     └──────────────────┘     │  → Trừ kho nguồn         │
       │                        │                  │  → Cộng kho đích          │
       │ Kiểm tra:              ▼                  └──────────────────────────┘
       │ - Tồn kho nguồn đủ?                               │
       │ - Kho nguồn ≠ Kho đích?                            ▼
       │                 ┌──────────────────┐     ┌──────────────────────────┐
       └─────────────────│  Hủy (pending)   │     │  Hủy (completed)         │
                         └──────────────────┘     │  → Cộng lại kho nguồn    │
                                                  │  → Trừ lại kho đích       │
                                                  └──────────────────────────┘
```

### 7.5. Chi tiết từng bước

#### Bước 1: Tạo phiếu chuyển kho
- **Ai thực hiện**: Admin tổng, Admin kho
- **Thao tác**:
  1. Chọn ngày chuyển
  2. Chọn kho nguồn (Admin tổng: bất kỳ, Admin kho: chỉ kho mình)
  3. Chọn kho đích (bất kỳ kho active nào, phải khác kho nguồn)
  4. Thêm danh sách vật tư và số lượng
- **Kiểm tra**: Tồn kho tại kho nguồn phải đủ
- **Validation**: `from_warehouse_id` ≠ `to_warehouse_id`

#### Bước 2: Duyệt phiếu chuyển kho
- **Ai thực hiện**: Admin tổng, Admin kho
- **Xử lý**:
  1. Trừ stock tại kho nguồn
  2. Cộng stock tại kho đích
  3. Cập nhật vị trí tại kho đích (nếu có)
  4. Kiểm tra cảnh báo tồn kho cho cả 2 kho

#### Bước 3: Hủy phiếu
- **Hủy pending**: Chỉ đổi trạng thái
- **Hủy completed**: Đảo ngược cả 2 thao tác (cộng lại kho nguồn, trừ lại kho đích)

### 7.6. Quyền thao tác

| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách | ✅ (tất cả) | ✅ (liên quan kho mình) | ✅ (liên quan kho mình) |
| Tạo phiếu | ✅ | ✅ | ✅ |
| Duyệt/Hủy | ✅ | ✅ | ❌ |

### 7.7. Phạm vi xem dữ liệu

- **Admin tổng**: Xem tất cả phiếu chuyển kho
- **Admin kho / Nhân viên kho**: Chỉ xem phiếu mà kho mình là kho nguồn HOẶC kho đích

---

## 8. Nghiệp vụ Kiểm kê kho

### 8.1. Mô tả

Kiểm kê kho là quy trình đối chiếu số lượng tồn kho thực tế (đếm tay) với số lượng trên hệ thống. Khi có chênh lệch, hệ thống tự động tạo phiếu nhập/xuất điều chỉnh.

### 8.2. Thuộc tính phiếu kiểm kê

| Trường | Mô tả |
|---|---|
| `warehouse_id` | Kho kiểm kê |
| `user_id` | Người thực hiện |
| `date` | Ngày kiểm kê |
| `status` | Trạng thái: `pending` / `completed` / `cancelled` |
| `note` | Ghi chú |

### 8.3. Chi tiết kiểm kê (InventoryCheckDetail)

| Trường | Mô tả |
|---|---|
| `material_id` | Vật tư |
| `system_stock` | Tồn kho trên hệ thống (tại thời điểm kiểm) |
| `actual_stock` | Tồn kho thực tế (đếm tay) |
| `variance` | Chênh lệch = actual_stock - system_stock |

### 8.4. Luồng nghiệp vụ

```
┌──────────────────────┐     ┌──────────────────┐     ┌────────────────────────────┐
│  Chọn kho & Kiểm đếm │────▶│  Chờ duyệt       │────▶│  Đã duyệt                  │
│  Nhập số thực tế      │     │  (pending)       │     │  (completed)               │
│  (pending)            │     │                  │     │                            │
└──────────────────────┘     └──────────────────┘     │  Tự động tạo:              │
                                    │                  │  • Phiếu NHẬP (nếu thừa)   │
                                    ▼                  │  • Phiếu XUẤT (nếu thiếu)  │
                             ┌──────────────────┐     │  → Cập nhật tồn kho         │
                             │  Hủy             │     └────────────────────────────┘
                             │  (cancelled)     │
                             │  Chỉ hủy pending │
                             └──────────────────┘
```

### 8.5. Chi tiết từng bước

#### Bước 1: Tạo phiếu kiểm kê
- **Ai thực hiện**: Admin tổng, Admin kho, Nhân viên kho
- **Thao tác**:
  1. Chọn kho cần kiểm kê
  2. Hệ thống tự động load danh sách vật tư có tồn kho tại kho đó (từ bảng `material_warehouses`)
  3. Với mỗi vật tư: hệ thống hiển thị `system_stock`, người dùng nhập `actual_stock`
  4. Hệ thống tự tính `variance = actual_stock - system_stock`
  5. Nhập ngày kiểm kê và ghi chú
- **Kết quả**: Phiếu kiểm kê trạng thái `pending`

#### Bước 2: Duyệt phiếu kiểm kê
- **Ai thực hiện**: Admin tổng, Admin kho
- **Xử lý tự động khi duyệt**:

  **Nếu có vật tư THỪA (variance > 0)**:
  - Tạo phiếu nhập kho tự động (status = `completed`)
  - Ghi chú: "Điều chỉnh kho (Kiểm kê #ID)"
  - Cộng số lượng chênh lệch vào tồn kho

  **Nếu có vật tư THIẾU (variance < 0)**:
  - Tạo phiếu xuất kho tự động (status = `completed`)
  - Ghi chú: "Điều chỉnh kho (Kiểm kê #ID)"
  - Trừ số lượng chênh lệch khỏi tồn kho

  **Nếu variance = 0**: Không tạo phiếu điều chỉnh

#### Bước 3: Hủy phiếu kiểm kê
- **Chỉ hủy được phiếu `pending`** (chưa duyệt)
- Phiếu đã duyệt (`completed`) KHÔNG thể hủy trực tiếp. Nếu muốn đảo ngược, phải tự hủy các phiếu nhập/xuất điều chỉnh tự động.

### 8.6. Quyền thao tác

| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách | ✅ | ✅ (kho mình) | ✅ (kho mình) |
| Tạo phiếu kiểm kê | ✅ | ✅ | ✅ |
| Duyệt phiếu | ✅ | ✅ | ❌ |
| Hủy phiếu | ✅ | ✅ | ❌ |

---

## 9. Hệ thống Cảnh báo tồn kho

### 9.1. Mô tả

Hệ thống tự động giám sát mức tồn kho và tạo cảnh báo khi tồn kho của một vật tư giảm xuống dưới mức tối thiểu (`min_stock_level`).

### 9.2. Thuộc tính cảnh báo (InventoryAlert)

| Trường | Mô tả |
|---|---|
| `material_id` | Vật tư bị cảnh báo |
| `current_stock` | Tồn kho tại thời điểm cảnh báo |
| `min_stock_level` | Mức tồn kho tối thiểu |
| `is_resolved` | Đã xử lý hay chưa (true/false) |

### 9.3. Cơ chế hoạt động

#### Tự động tạo cảnh báo
- Sau **mỗi lần cập nhật tồn kho** (nhập, xuất, chuyển, kiểm kê), hệ thống kiểm tra:
  - Tổng tồn kho của vật tư (tất cả các kho) < `min_stock_level`?
  - Nếu CÓ → Tạo hoặc cập nhật cảnh báo chưa xử lý
  - Nếu KHÔNG (tồn kho đã an toàn) → Tự động đánh dấu cảnh báo cũ là đã xử lý

#### Xử lý thủ công
- Admin tổng / Admin kho có thể đánh dấu cảnh báo là "Đã xử lý" thủ công

### 9.4. Quyền thao tác

| Thao tác | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Xem danh sách cảnh báo | ✅ | ✅ | ❌ |
| Đánh dấu đã xử lý | ✅ | ✅ | ❌ |

---

## 10. Báo cáo & Xuất dữ liệu

### 10.1. Báo cáo tồn kho

#### Mô tả
Hiển thị bảng tổng hợp tồn kho của tất cả vật tư tại các kho, bao gồm: tên vật tư, đơn vị tính, kho, số lượng tồn, vị trí, giá vốn bình quân.

#### Bộ lọc
- **Admin tổng**: Có thể lọc theo kho cụ thể hoặc xem tất cả
- **Admin kho / Nhân viên kho**: Chỉ xem dữ liệu kho của mình

#### Xuất file
| Định dạng | Mô tả |
|---|---|
| Excel (.xlsx) | Xuất báo cáo tồn kho dạng bảng tính |
| PDF | Xuất báo cáo tồn kho dạng PDF (landscape A4) |

### 10.2. Xuất danh sách phiếu nhập kho

| Định dạng | Tên file |
|---|---|
| Excel | `danh-sach-phieu-nhap-YYYYMMDD-HHmm.xlsx` |
| PDF | `danh-sach-phieu-nhap-YYYYMMDD-HHmm.pdf` |

### 10.3. Xuất danh sách phiếu xuất kho

| Định dạng | Tên file |
|---|---|
| Excel | `danh-sach-phieu-xuat-YYYYMMDD-HHmm.xlsx` |
| PDF | `danh-sach-phieu-xuat-YYYYMMDD-HHmm.pdf` |

### 10.4. Xuất danh sách vật tư

| Định dạng | Tên file |
|---|---|
| Excel | `danh-sach-vat-tu.xlsx` |

### 10.5. Quyền xuất báo cáo

| Báo cáo | Admin tổng | Admin kho | Nhân viên kho |
|---|---|---|---|
| Báo cáo tồn kho | ✅ | ✅ | ✅ |
| Export Excel/PDF tồn kho | ✅ | ✅ | ✅ |
| Export phiếu nhập | ✅ | ✅ | ✅ |
| Export phiếu xuất | ✅ | ✅ | ✅ |
| Export danh sách vật tư | ✅ | ✅ | ❌ |

---

## 11. Quản lý Người dùng

### 11.1. Mô tả

Chỉ **Admin tổng** mới có quyền quản lý người dùng (CRUD) và phân quyền.

### 11.2. Thuộc tính người dùng

| Trường | Mô tả | Bắt buộc |
|---|---|---|
| `name` | Họ tên | ✅ |
| `email` | Email (unique, dùng đăng nhập) | ✅ |
| `password` | Mật khẩu (min 8 ký tự, hashed) | ✅ (khi tạo) |
| `role` | Vai trò: `Admin tổng` / `Admin kho` / `Nhân viên kho` | ✅ |
| `warehouse_id` | Kho được gán (bắt buộc cho Admin kho & Nhân viên kho) | ❌ |
| `status` | Trạng thái: `active` / `inactive` | ✅ |

### 11.3. Thao tác

| Thao tác | Mô tả |
|---|---|
| Tạo người dùng | Nhập đầy đủ thông tin, gán role và kho |
| Sửa người dùng | Cập nhật thông tin, có thể đổi role/kho. Mật khẩu chỉ cập nhật nếu nhập mới |
| Xóa người dùng | Xóa tài khoản. Không thể tự xóa tài khoản đang đăng nhập |

### 11.4. Quản lý phân quyền

- **Trang phân quyền** (`/permissions`): Hiển thị ma trận quyền theo role
- Admin tổng có thể bật/tắt từng quyền cho từng role
- Dữ liệu phân quyền lưu trong bảng `role_permissions`
- Nếu DB chưa có dữ liệu → fallback từ file config `config/permissions.php`

---

## 12. Dashboard & Thống kê

### 12.1. Mô tả

Trang chủ (Dashboard) hiển thị tổng quan tình hình kho hàng, dành cho tất cả user đã đăng nhập.

### 12.2. Thông tin hiển thị

#### Thống kê tổng quan
| Chỉ số | Mô tả |
|---|---|
| Tổng số kho | Số lượng kho hàng (Admin tổng: tất cả, khác: chỉ kho mình) |
| Tổng số vật tư | Tổng số loại vật tư trong hệ thống |
| Tổng phiếu nhập | Số phiếu nhập kho |
| Tổng phiếu xuất | Số phiếu xuất kho |
| Tổng giá trị tồn kho | SUM(stock × average_cost) |

#### Hoạt động gần đây
- 5 phiếu nhập kho mới nhất
- 5 phiếu xuất kho mới nhất

#### Cảnh báo tồn kho thấp
- Danh sách vật tư có tồn kho < mức tối thiểu (`min_stock`)
- Hiển thị: tên vật tư, kho, tồn hiện tại, mức tối thiểu, vị trí

#### Biểu đồ xu hướng 7 ngày
- Số phiếu nhập/xuất đã duyệt (`completed`) theo từng ngày trong 7 ngày gần nhất

#### Phân bổ tồn kho theo kho
- Biểu đồ tổng tồn kho (SUM stock) tại mỗi kho

### 12.3. Phạm vi dữ liệu

- **Admin tổng**: Xem dữ liệu tất cả các kho
- **Admin kho / Nhân viên kho**: Chỉ xem dữ liệu kho được gán

---

## 13. Sơ đồ luồng nghiệp vụ

### 13.1. Luồng tổng quan hệ thống

```
                    ┌─────────────────────────────────────────┐
                    │            ĐĂNG NHẬP                     │
                    │  (Email + Password → Session Auth)       │
                    └─────────────────┬───────────────────────┘
                                      │
                                      ▼
                    ┌─────────────────────────────────────────┐
                    │            DASHBOARD                     │
                    │  (Thống kê, Cảnh báo, Biểu đồ)         │
                    └─────────────────┬───────────────────────┘
                                      │
              ┌───────────┬───────────┼───────────┬───────────┐
              ▼           ▼           ▼           ▼           ▼
        ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
        │ Danh mục │ │ Nhập kho │ │ Xuất kho │ │Chuyển kho│ │ Kiểm kê  │
        │          │ │          │ │          │ │          │ │          │
        │• Vật tư  │ │• Tạo     │ │• Tạo     │ │• Tạo     │ │• Tạo     │
        │• ĐVT     │ │• Duyệt   │ │• Duyệt   │ │• Duyệt   │ │• Duyệt   │
        │• NCC     │ │• Hủy     │ │• Hủy     │ │• Hủy     │ │• Hủy     │
        │• CT/DA   │ │• Export  │ │• Export  │ │          │ │          │
        └──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘
              │           │           │           │           │
              └───────────┴───────────┼───────────┴───────────┘
                                      ▼
                    ┌─────────────────────────────────────────┐
                    │         TỒN KHO (MaterialWarehouse)     │
                    │  • Stock (số lượng)                      │
                    │  • Average Cost (giá vốn BQ)            │
                    │  • Location (vị trí)                     │
                    └─────────────────┬───────────────────────┘
                                      │
                              ┌───────┴───────┐
                              ▼               ▼
                    ┌──────────────┐  ┌──────────────┐
                    │  Cảnh báo    │  │  Báo cáo     │
                    │  tồn kho     │  │  & Export    │
                    └──────────────┘  └──────────────┘
```

### 13.2. Luồng phê duyệt phiếu (Approval Flow)

```
┌──────────────────────────────────────────────────────────────────────┐
│                     QUY TRÌNH PHÊ DUYỆT PHIẾU                        │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  [Nhân viên kho / Admin kho / Admin tổng]                           │
│           │                                                          │
│           ▼                                                          │
│  ┌─────────────────┐                                                │
│  │  TẠO PHIẾU      │  → Trạng thái: PENDING                        │
│  │  (Nhập/Xuất/    │  → Kiểm tra: tồn kho, định mức               │
│  │   Chuyển/Kiểm)  │  → Tồn kho: CHƯA thay đổi                    │
│  └────────┬────────┘                                                │
│           │                                                          │
│           ▼                                                          │
│  [Admin tổng / Admin kho]                                           │
│           │                                                          │
│     ┌─────┴─────┐                                                   │
│     ▼           ▼                                                    │
│  ┌────────┐  ┌────────┐                                            │
│  │ DUYỆT  │  │  HỦY   │                                            │
│  └───┬────┘  └───┬────┘                                            │
│      │           │                                                   │
│      ▼           ▼                                                   │
│  COMPLETED    CANCELLED                                              │
│  → Cập nhật   → Nếu đã completed:                                  │
│    tồn kho      hoàn trả tồn kho                                   │
│  → Tính giá  → Nếu pending:                                        │
│    vốn BQ      không ảnh hưởng                                      │
│  → Kiểm tra                                                         │
│    cảnh báo                                                          │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

### 13.3. Luồng kiểm soát xuất kho theo định mức (BoQ)

```
┌──────────────────────────────────────────────────────────────────────┐
│              KIỂM SOÁT XUẤT KHO THEO ĐỊNH MỨC (BoQ)                 │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. Admin tổng / Admin kho thiết lập DỰ TOÁN cho công trình:       │
│     ┌─────────────────────────────────────────────┐                 │
│     │ Công trình A:                                │                 │
│     │   • Xi măng: 100 tấn                        │                 │
│     │   • Thép: 50 tấn                            │                 │
│     │   • Gạch: 10,000 viên                       │                 │
│     └─────────────────────────────────────────────┘                 │
│                                                                      │
│  2. Khi tạo phiếu xuất cho Công trình A:                           │
│     ┌─────────────────────────────────────────────┐                 │
│     │ Kiểm tra 1: Vật tư có trong dự toán?        │                 │
│     │   → Không có → ❌ Từ chối                    │                 │
│     │                                              │                 │
│     │ Kiểm tra 2: Tồn kho đủ?                     │                 │
│     │   → Không đủ → ❌ Từ chối                    │                 │
│     │                                              │                 │
│     │ Kiểm tra 3: Vượt định mức?                   │                 │
│     │   Đã xuất (completed + pending) + Yêu cầu   │                 │
│     │   ≤ Định mức dự toán?                        │                 │
│     │   → Vượt → ❌ Từ chối                        │                 │
│     │   → Không vượt → ✅ Cho phép tạo phiếu       │                 │
│     └─────────────────────────────────────────────┘                 │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 14. Ma trận phân quyền chi tiết

### 14.1. Nhóm: Quản lý Danh mục

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Xem danh sách vật tư | ✅ | ✅ | ✅ |
| Thêm / Sửa / Xóa vật tư | ✅ | ✅ | ❌ |
| Quản lý đơn vị tính | ✅ | ✅ | ❌ |
| Quản lý nhà cung cấp | ✅ | ✅ | ❌ |
| Quản lý công trình | ✅ | ✅ | ❌ |

### 14.2. Nhóm: Quản lý Kho

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Xem danh sách kho | ✅ | ✅ | ✅ |
| Thêm / Sửa / Xóa kho | ✅ | ❌ | ❌ |

### 14.3. Nhóm: Nghiệp vụ Nhập kho

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Tạo phiếu nhập kho | ✅ | ✅ | ✅ |
| Duyệt / Hủy phiếu nhập kho | ✅ | ✅ | ❌ |
| Xuất Excel / PDF nhập kho | ✅ | ✅ | ✅ |

### 14.4. Nhóm: Nghiệp vụ Xuất kho

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Tạo phiếu xuất kho | ✅ | ✅ | ✅ |
| Duyệt / Hủy phiếu xuất kho | ✅ | ✅ | ❌ |
| Xuất Excel / PDF xuất kho | ✅ | ✅ | ✅ |

### 14.5. Nhóm: Nghiệp vụ Chuyển kho

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Tạo phiếu chuyển kho | ✅ | ✅ | ✅ |
| Duyệt / Hủy phiếu chuyển kho | ✅ | ✅ | ❌ |

### 14.6. Nhóm: Kiểm kê kho

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Tạo phiếu kiểm kê | ✅ | ✅ | ✅ |
| Duyệt / Hủy phiếu kiểm kê | ✅ | ✅ | ❌ |

### 14.7. Nhóm: Báo cáo

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Xem báo cáo tồn kho | ✅ | ✅ | ✅ |
| Xuất báo cáo Excel / PDF | ✅ | ✅ | ✅ |

### 14.8. Nhóm: Cảnh báo Tồn kho

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Xem cảnh báo tồn kho | ✅ | ✅ | ❌ |
| Xử lý cảnh báo tồn kho | ✅ | ✅ | ❌ |

### 14.9. Nhóm: Quản lý Người dùng

| Quyền | Admin tổng | Admin kho | Nhân viên kho |
|---|:---:|:---:|:---:|
| Xem danh sách người dùng | ✅ | ❌ | ❌ |
| Thêm / Sửa / Xóa người dùng | ✅ | ❌ | ❌ |
| Phân quyền người dùng | ✅ | ❌ | ❌ |

---

## 15. Chi tiết hoạt động theo từng Role

### 15.1. Admin tổng - Quản trị viên cấp cao

#### Mô tả vai trò
Admin tổng là người có quyền cao nhất trong hệ thống, quản lý toàn bộ hoạt động của tất cả các kho hàng. Thường là Giám đốc, Trưởng phòng vật tư, hoặc người phụ trách chung.

#### Danh sách chức năng

| STT | Chức năng | Mô tả chi tiết |
|---|---|---|
| 1 | Dashboard toàn hệ thống | Xem thống kê tổng hợp tất cả các kho: tổng giá trị, số phiếu, cảnh báo |
| 2 | Quản lý kho hàng | Tạo mới, sửa, xóa kho. Gán người quản lý (Admin kho) cho từng kho |
| 3 | Quản lý người dùng | Tạo tài khoản, gán role, gán kho, kích hoạt/vô hiệu hóa tài khoản |
| 4 | Phân quyền | Cấu hình ma trận phân quyền: bật/tắt từng quyền cho từng role |
| 5 | Quản lý vật tư | Thêm/sửa/xóa vật tư, import/export Excel |
| 6 | Quản lý đơn vị tính | Thêm/sửa/xóa đơn vị tính |
| 7 | Quản lý nhà cung cấp | Thêm/sửa/xóa NCC, gán NCC cho kho bất kỳ |
| 8 | Quản lý công trình | Thêm/sửa/xóa công trình, gán kho, thiết lập dự toán BoQ |
| 9 | Tạo phiếu nhập kho | Tạo phiếu nhập cho BẤT KỲ kho nào |
| 10 | Duyệt/Hủy phiếu nhập | Phê duyệt hoặc hủy phiếu nhập của tất cả các kho |
| 11 | Tạo phiếu xuất kho | Tạo phiếu xuất cho bất kỳ kho nào |
| 12 | Duyệt/Hủy phiếu xuất | Phê duyệt hoặc hủy phiếu xuất |
| 13 | Tạo phiếu chuyển kho | Chuyển vật tư giữa bất kỳ 2 kho nào |
| 14 | Duyệt/Hủy phiếu chuyển | Phê duyệt hoặc hủy phiếu chuyển kho |
| 15 | Tạo phiếu kiểm kê | Kiểm kê bất kỳ kho nào |
| 16 | Duyệt/Hủy phiếu kiểm kê | Phê duyệt kiểm kê, hệ thống tự điều chỉnh tồn kho |
| 17 | Xem cảnh báo tồn kho | Xem và xử lý cảnh báo tồn kho toàn hệ thống |
| 18 | Báo cáo tồn kho | Xem báo cáo tất cả kho, lọc theo kho, export Excel/PDF |
| 19 | Export phiếu nhập/xuất | Xuất danh sách phiếu ra Excel/PDF |

#### Luồng hoạt động hàng ngày điển hình

```
1. Đăng nhập → Xem Dashboard tổng quan
2. Kiểm tra cảnh báo tồn kho → Xử lý (đặt hàng NCC)
3. Duyệt các phiếu nhập/xuất/chuyển đang chờ
4. Kiểm tra báo cáo tồn kho
5. Quản lý danh mục (nếu cần thêm vật tư/NCC/công trình mới)
6. Quản lý người dùng (nếu có nhân sự mới)
```

---

### 15.2. Admin kho - Quản lý kho

#### Mô tả vai trò
Admin kho là người quản lý trực tiếp một kho hàng cụ thể. Có quyền duyệt phiếu, quản lý danh mục, nhưng chỉ trong phạm vi kho được gán.

#### Danh sách chức năng

| STT | Chức năng | Mô tả chi tiết |
|---|---|---|
| 1 | Dashboard kho mình | Xem thống kê chỉ của kho được gán |
| 2 | Xem kho hàng | Xem thông tin kho (không được sửa/xóa/tạo kho) |
| 3 | Quản lý vật tư | Thêm/sửa/xóa vật tư, import/export |
| 4 | Quản lý đơn vị tính | Thêm/sửa/xóa |
| 5 | Quản lý nhà cung cấp | Thêm/sửa/xóa NCC (tự động gán kho mình) |
| 6 | Quản lý công trình | Thêm/sửa/xóa công trình, thiết lập dự toán BoQ |
| 7 | Tạo phiếu nhập kho | Tạo phiếu nhập CHỈ cho kho mình |
| 8 | Duyệt/Hủy phiếu nhập | Duyệt/hủy phiếu nhập của kho mình |
| 9 | Tạo phiếu xuất kho | Tạo phiếu xuất CHỈ từ kho mình |
| 10 | Duyệt/Hủy phiếu xuất | Duyệt/hủy phiếu xuất của kho mình |
| 11 | Tạo phiếu chuyển kho | Chuyển từ kho mình sang kho khác |
| 12 | Duyệt/Hủy phiếu chuyển | Duyệt/hủy phiếu chuyển liên quan kho mình |
| 13 | Tạo phiếu kiểm kê | Kiểm kê kho mình |
| 14 | Duyệt/Hủy phiếu kiểm kê | Duyệt kiểm kê kho mình |
| 15 | Xem cảnh báo tồn kho | Xem và xử lý cảnh báo |
| 16 | Báo cáo tồn kho | Xem báo cáo kho mình, export Excel/PDF |
| 17 | Export phiếu | Xuất danh sách phiếu nhập/xuất |

#### Luồng hoạt động hàng ngày điển hình

```
1. Đăng nhập → Xem Dashboard kho mình
2. Kiểm tra cảnh báo tồn kho thấp
3. Duyệt phiếu nhập/xuất/chuyển do nhân viên tạo
4. Tạo phiếu nhập khi nhận hàng từ NCC
5. Tạo phiếu xuất khi cấp vật tư cho công trình
6. Kiểm kê định kỳ
7. Xuất báo cáo tồn kho cuối ngày/tuần
```

---

### 15.3. Nhân viên kho - Nhân viên thực hiện

#### Mô tả vai trò
Nhân viên kho là người trực tiếp thực hiện các nghiệp vụ nhập/xuất hàng ngày. Chỉ có quyền tạo phiếu và xem dữ liệu, KHÔNG có quyền duyệt hay quản lý danh mục.

#### Danh sách chức năng

| STT | Chức năng | Mô tả chi tiết |
|---|---|---|
| 1 | Dashboard kho mình | Xem thống kê cơ bản của kho được gán |
| 2 | Xem vật tư | Xem danh sách và chi tiết vật tư (không sửa/xóa) |
| 3 | Xem đơn vị tính | Xem danh sách (không sửa/xóa) |
| 4 | Xem nhà cung cấp | Xem danh sách NCC thuộc kho mình |
| 5 | Xem công trình | Xem danh sách công trình thuộc kho mình |
| 6 | Xem kho hàng | Xem thông tin kho |
| 7 | Tạo phiếu nhập kho | Tạo phiếu nhập cho kho mình (chờ duyệt) |
| 8 | Tạo phiếu xuất kho | Tạo phiếu xuất từ kho mình (chờ duyệt) |
| 9 | Tạo phiếu chuyển kho | Tạo phiếu chuyển từ kho mình (chờ duyệt) |
| 10 | Tạo phiếu kiểm kê | Kiểm kê kho mình (chờ duyệt) |
| 11 | Xem phiếu nhập/xuất/chuyển/kiểm kê | Xem danh sách và chi tiết phiếu |
| 12 | Báo cáo tồn kho | Xem báo cáo kho mình |
| 13 | Export phiếu | Xuất phiếu nhập/xuất ra Excel/PDF |

#### Luồng hoạt động hàng ngày điển hình

```
1. Đăng nhập → Xem Dashboard
2. Nhận hàng từ NCC → Tạo phiếu nhập (pending) → Chờ Admin kho duyệt
3. Nhận yêu cầu xuất vật tư → Tạo phiếu xuất (pending) → Chờ duyệt
4. Kiểm đếm hàng → Tạo phiếu kiểm kê → Chờ duyệt
5. Xem trạng thái phiếu đã tạo
```

#### Hạn chế

| Không được phép | Lý do |
|---|---|
| Duyệt/Hủy phiếu | Cần cấp quản lý phê duyệt |
| Thêm/Sửa/Xóa danh mục | Tránh sai sót dữ liệu master |
| Quản lý kho/người dùng | Chức năng quản trị cấp cao |
| Xem cảnh báo tồn kho | Trách nhiệm của quản lý |
| Phân quyền | Chức năng Admin tổng |

---

## 16. Quy tắc nghiệp vụ tổng hợp

### 16.1. Nguyên tắc bất biến (Immutability)

- Phiếu nhập/xuất/chuyển/kiểm kê **KHÔNG được phép sửa** sau khi tạo
- Phiếu **KHÔNG được phép xóa vĩnh viễn**, chỉ có thể Hủy (soft cancel)
- Mọi thay đổi tồn kho đều có phiếu ghi nhận (audit trail)

### 16.2. Nguyên tắc phê duyệt

- Mọi phiếu khi tạo đều ở trạng thái `pending` (chờ duyệt)
- Tồn kho chỉ thay đổi khi phiếu được duyệt (`completed`)
- Chỉ Admin tổng và Admin kho mới có quyền duyệt/hủy
- Có thể hủy phiếu đã duyệt (sẽ hoàn trả tồn kho)

### 16.3. Nguyên tắc phạm vi dữ liệu (Data Scope)

- User thuộc kho nào chỉ xem/thao tác dữ liệu kho đó
- Ngoại lệ: Admin tổng xem/thao tác tất cả các kho
- Nhà cung cấp/Công trình có `warehouse_id = NULL` được xem bởi tất cả

### 16.4. Nguyên tắc kiểm soát xuất kho

- Xuất kho phải gắn với công trình cụ thể
- Vật tư phải có trong dự toán (BoQ) của công trình
- Không được xuất vượt định mức dự toán
- Tính toán bao gồm cả phiếu pending (chưa duyệt) để tránh vượt mức

### 16.5. Nguyên tắc tính giá vốn

- Sử dụng phương pháp **Bình quân gia quyền (Weighted Average)**
- Giá vốn được tính lại mỗi khi nhập kho với đơn giá mới
- Công thức: `(Tồn cũ × Giá cũ + SL mới × Giá mới) / (Tồn cũ + SL mới)`

### 16.6. Nguyên tắc cảnh báo

- Cảnh báo tự động khi tổng tồn kho (tất cả kho) < `min_stock_level`
- Cảnh báo tự động được giải quyết khi tồn kho trở lại mức an toàn
- Admin có thể đánh dấu xử lý thủ công

---

## 17. Danh sách API Routes

### 17.1. Routes công khai (không cần auth)

| Method | URL | Mô tả |
|---|---|---|
| GET | `/login` | Trang đăng nhập |
| POST | `/login` | Xử lý đăng nhập |
| POST | `/logout` | Đăng xuất |
| GET | `/register` | Trang đăng ký |
| POST | `/register` | Xử lý đăng ký |
| GET | `/password/reset` | Quên mật khẩu |

### 17.2. Routes yêu cầu đăng nhập (auth)

| Method | URL | Controller | Quyền |
|---|---|---|---|
| GET | `/` | DashboardController@index | Tất cả |
| GET | `/materials` | MaterialController@index | Tất cả |
| GET | `/materials/{id}` | MaterialController@show | Tất cả |
| GET | `/units` | UnitController@index | Tất cả |
| GET | `/suppliers` | SupplierController@index | Tất cả |
| GET | `/projects` | ProjectController@index | Tất cả |
| GET | `/warehouses` | WarehouseController@index | Tất cả |
| GET | `/warehouses/{id}` | WarehouseController@show | Tất cả |
| GET | `/reports/inventory` | ReportController@inventory | Tất cả |

### 17.3. Routes yêu cầu Admin tổng + Admin kho

| Method | URL | Controller | Mô tả |
|---|---|---|---|
| GET | `/materials/create` | MaterialController@create | Form tạo vật tư |
| POST | `/materials` | MaterialController@store | Lưu vật tư mới |
| GET | `/materials/{id}/edit` | MaterialController@edit | Form sửa vật tư |
| PUT | `/materials/{id}` | MaterialController@update | Cập nhật vật tư |
| DELETE | `/materials/{id}` | MaterialController@destroy | Xóa vật tư |
| GET | `/materials-export` | MaterialController@export | Export Excel |
| POST | `/materials-import` | MaterialController@import | Import Excel |
| POST | `/inventory-entries/{id}/approve` | InventoryEntryController@approve | Duyệt phiếu nhập |
| POST | `/inventory-entries/{id}/cancel` | InventoryEntryController@cancel | Hủy phiếu nhập |
| POST | `/inventory-exits/{id}/approve` | InventoryExitController@approve | Duyệt phiếu xuất |
| POST | `/inventory-exits/{id}/cancel` | InventoryExitController@cancel | Hủy phiếu xuất |
| POST | `/inventory-transfers/{id}/approve` | InventoryTransferController@approve | Duyệt chuyển kho |
| POST | `/inventory-transfers/{id}/cancel` | InventoryTransferController@cancel | Hủy chuyển kho |
| POST | `/inventory-checks/{id}/approve` | InventoryCheckController@approve | Duyệt kiểm kê |
| POST | `/inventory-checks/{id}/cancel` | InventoryCheckController@cancel | Hủy kiểm kê |
| GET | `/inventory-alerts` | InventoryAlertController@index | Xem cảnh báo |
| POST | `/inventory-alerts/{id}/resolve` | InventoryAlertController@resolve | Xử lý cảnh báo |

### 17.4. Routes chỉ Admin tổng

| Method | URL | Controller | Mô tả |
|---|---|---|---|
| GET | `/warehouses/create` | WarehouseController@create | Form tạo kho |
| POST | `/warehouses` | WarehouseController@store | Lưu kho mới |
| PUT | `/warehouses/{id}` | WarehouseController@update | Cập nhật kho |
| DELETE | `/warehouses/{id}` | WarehouseController@destroy | Xóa kho |
| GET | `/users` | UserController@index | Danh sách user |
| GET | `/users/create` | UserController@create | Form tạo user |
| POST | `/users` | UserController@store | Lưu user mới |
| GET | `/users/{id}/edit` | UserController@edit | Form sửa user |
| PUT | `/users/{id}` | UserController@update | Cập nhật user |
| DELETE | `/users/{id}` | UserController@destroy | Xóa user |
| GET | `/permissions` | PermissionController@index | Ma trận phân quyền |
| POST | `/permissions` | PermissionController@update | Lưu phân quyền |

---

## 18. Mô hình dữ liệu (Entity Relationship)

### 18.1. Bảng chính

| Bảng | Mô tả | Quan hệ chính |
|---|---|---|
| `users` | Người dùng | belongsTo Warehouse |
| `warehouses` | Kho hàng | belongsTo User (manager), hasMany Users |
| `materials` | Vật tư | belongsTo Unit, belongsToMany Warehouses |
| `units` | Đơn vị tính | hasMany Materials |
| `suppliers` | Nhà cung cấp | belongsTo Warehouse, hasMany InventoryEntries |
| `projects` | Công trình | belongsTo Warehouse, hasMany InventoryExits |
| `material_warehouses` | Tồn kho theo kho | belongsTo Material, belongsTo Warehouse |
| `project_materials` | Dự toán công trình | belongsTo Project, belongsTo Material |
| `role_permissions` | Phân quyền theo role | - |

### 18.2. Bảng giao dịch

| Bảng | Mô tả | Chi tiết |
|---|---|---|
| `inventory_entries` | Phiếu nhập kho | hasMany InventoryEntryDetails |
| `inventory_entry_details` | Chi tiết phiếu nhập | belongsTo InventoryEntry, Material |
| `inventory_exits` | Phiếu xuất kho | hasMany InventoryExitDetails |
| `inventory_exit_details` | Chi tiết phiếu xuất | belongsTo InventoryExit, Material |
| `inventory_transfers` | Phiếu chuyển kho | hasMany InventoryTransferDetails |
| `inventory_transfer_details` | Chi tiết chuyển kho | belongsTo InventoryTransfer, Material |
| `inventory_checks` | Phiếu kiểm kê | hasMany InventoryCheckDetails |
| `inventory_check_details` | Chi tiết kiểm kê | belongsTo InventoryCheck, Material |
| `inventory_alerts` | Cảnh báo tồn kho | belongsTo Material |

---

## 19. Trạng thái phiếu (Status Lifecycle)

### 19.1. Các trạng thái

| Trạng thái | Mã | Mô tả |
|---|---|---|
| Chờ duyệt | `pending` | Phiếu vừa tạo, chưa ảnh hưởng tồn kho |
| Đã duyệt | `completed` | Phiếu đã được phê duyệt, tồn kho đã cập nhật |
| Đã hủy | `cancelled` | Phiếu bị hủy, tồn kho được hoàn trả (nếu đã duyệt trước đó) |

### 19.2. Chuyển đổi trạng thái hợp lệ

```
pending ──────────▶ completed (Duyệt)
pending ──────────▶ cancelled (Hủy từ pending)
completed ────────▶ cancelled (Hủy từ completed → hoàn trả tồn kho)
cancelled ────────▶ (KHÔNG thể chuyển sang trạng thái khác)
```

### 19.3. Tác động lên tồn kho

| Loại phiếu | Khi duyệt (→ completed) | Khi hủy completed (→ cancelled) |
|---|---|---|
| Nhập kho | + stock (cộng) | - stock (trừ lại) |
| Xuất kho | - stock (trừ) | + stock (cộng lại) |
| Chuyển kho | - stock kho nguồn, + stock kho đích | + stock kho nguồn, - stock kho đích |
| Kiểm kê | Tự tạo phiếu nhập/xuất điều chỉnh | Không hỗ trợ hủy completed |

---

*Tài liệu được tạo tự động từ source code ngày 19/05/2026*
*Phiên bản: 1.0*
