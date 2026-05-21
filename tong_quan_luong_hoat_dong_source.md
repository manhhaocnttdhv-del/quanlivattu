# Tổng quan luồng hoạt động source code

## 1. Kiến trúc tổng thể

Source code là ứng dụng Laravel 10 theo mô hình MVC:

- `routes/web.php`: khai báo URL, middleware đăng nhập và phân quyền.
- `app/Http/Controllers`: nhận request, validate dữ liệu, gọi model/service, trả view.
- `app/Models`: ánh xạ các bảng dữ liệu và quan hệ Eloquent.
- `app/Services/InventoryService.php`: xử lý nghiệp vụ cập nhật tồn kho tập trung.
- `resources/views`: giao diện Blade, dùng layout AdminLTE.
- `database/migrations`: định nghĩa cấu trúc bảng.
- `app/Imports`, `app/Exports`: nhập/xuất Excel, PDF báo cáo.

Luồng request cơ bản:

```text
Browser
  -> public/index.php
  -> Laravel Kernel + middleware web/auth/role
  -> routes/web.php
  -> Controller
  -> Model / InventoryService / Export / Import
  -> Blade view hoặc file Excel/PDF
  -> Response về browser
```

## 2. Luồng đăng nhập và phân quyền

Ứng dụng dùng `Auth::routes()` của Laravel UI cho đăng nhập, đăng ký, quên mật khẩu.

Sau khi đăng nhập:

- Người dùng vào `/`, route này trỏ đến `DashboardController@index`.
- `RouteServiceProvider::HOME` cũng đặt về `/`.
- Các route nghiệp vụ chính nằm trong `Route::middleware(['auth'])->group(...)`, nên phải đăng nhập mới truy cập được.

Phân quyền chính dựa trên field `users.role`:

- `Admin tổng`: toàn quyền, xem toàn bộ kho, quản lý người dùng, phân quyền, kho hàng.
- `Admin kho`: thao tác trong kho được gán, có quyền quản lý nghiệp vụ kho.
- `Nhân viên kho`: chủ yếu xem/tạo phiếu, không có quyền duyệt/hủy ở các nghiệp vụ quan trọng.

Middleware `CheckRole` nhận danh sách role từ route:

```text
Request
  -> auth middleware kiểm tra đăng nhập
  -> role middleware kiểm tra user.role
  -> nếu hợp lệ thì vào controller
  -> nếu không hợp lệ thì abort 403
```

Ngoài middleware route, model `User` còn có các helper:

- `hasRole()`
- `isAdminTong()`
- `isAdminKho()`
- `isNhanVienKho()`
- `hasPermission()`

Ma trận phân quyền lưu trong `config/permissions.php` và có thể ghi ra bảng `role_permissions` qua `PermissionController`.

## 3. Luồng giao diện

Layout chính nằm ở `resources/views/layouts/admin.blade.php`.

Layout này tạo:

- Header, sidebar, menu người dùng.
- Menu quản lý: kho hàng, vật tư, nhóm vật tư, đơn vị tính, nhà cung cấp, công trình, người dùng, phân quyền.
- Menu nghiệp vụ: nhập kho, xuất kho, chuyển kho.
- Menu kiểm kê và báo cáo.

Mỗi controller trả về một Blade view tương ứng, ví dụ:

- `MaterialController@index` -> `resources/views/materials/index.blade.php`
- `InventoryEntryController@index` -> `resources/views/inventory_entries/index.blade.php`
- `ReportController@inventory` -> `resources/views/reports/inventory.blade.php`

## 4. Nhóm dữ liệu nền

Các dữ liệu nền dùng để vận hành kho gồm:

- `warehouses`: kho hàng.
- `users`: người dùng, role, kho được gán.
- `units`: đơn vị tính.
- `categories`: nhóm vật tư, hỗ trợ nhóm cha - nhóm con một cấp.
- `materials`: vật tư, đơn vị tính, nhóm vật tư, tồn tối thiểu/tối đa.
- `suppliers`: nhà cung cấp.
- `projects`: công trình/dự án.
- `project_materials`: định mức vật tư cho từng công trình.

Luồng quản lý dữ liệu nền:

```text
User mở màn hình danh mục
  -> Controller index/create/edit
  -> validate dữ liệu khi store/update
  -> Model tạo/cập nhật/xóa record
  -> redirect về danh sách kèm thông báo
```

Một số ràng buộc nghiệp vụ đáng chú ý:

- Không xóa `Unit` nếu đang được `Material` sử dụng.
- Không xóa `Supplier` nếu đã có phiếu nhập.
- Không xóa `Project` nếu đã có phiếu xuất.
- `Category` chỉ hỗ trợ phân cấp một bậc.
- User không phải `Admin tổng` khi tạo nhà cung cấp/công trình sẽ tự gán theo `warehouse_id` của user.

## 5. Bảng tồn kho trung tâm

Bảng tồn thực tế là `material_warehouses`.

Mỗi dòng biểu diễn tồn của một vật tư tại một kho:

```text
warehouse_id
material_id
stock
location
average_cost
```

Các phiếu nhập/xuất/chuyển/kiểm kê không nên tự sửa tồn trực tiếp. Source đang gom phần cập nhật tồn vào:

```text
app/Services/InventoryService.php
```

Service này có hai hàm chính:

- `updateStock($warehouseId, $materialId, $quantity, $type, $unitPrice, $location)`
- `getStock($warehouseId, $materialId)`

Khi cập nhật tồn:

- Nếu `type = add`: cộng tồn, đồng thời tính lại giá vốn trung bình nếu có đơn giá.
- Nếu `type = subtract`: kiểm tra đủ tồn rồi trừ tồn.
- Nếu tồn tổng của vật tư thấp hơn mức tối thiểu thì tạo/cập nhật cảnh báo tồn kho.
- Nếu tồn đã đạt mức an toàn thì tự đánh dấu cảnh báo cũ là đã xử lý.

## 6. Luồng nhập kho

Controller chính: `InventoryEntryController`.

Luồng tạo phiếu:

```text
Người dùng vào Nhập kho
  -> chọn ngày, kho, nhà cung cấp, danh sách vật tư, số lượng, đơn giá, vị trí
  -> store() validate dữ liệu
  -> tạo inventory_entries với status = pending
  -> tạo nhiều inventory_entry_details
  -> chưa cập nhật tồn kho
```

Luồng duyệt phiếu:

```text
Admin tổng/Admin kho bấm duyệt
  -> approve()
  -> kiểm tra phiếu đang pending
  -> duyệt từng detail
  -> InventoryService::updateStock(..., type = add)
  -> cộng tồn vào material_warehouses
  -> tính average_cost nếu có price
  -> cập nhật status = completed
```

Luồng hủy phiếu:

```text
Nếu phiếu pending
  -> chỉ đổi status = cancelled

Nếu phiếu completed
  -> trừ ngược số lượng đã nhập qua InventoryService
  -> đổi status = cancelled
```

## 7. Luồng xuất kho

Controller chính: `InventoryExitController`.

Phiếu xuất gắn với công trình/dự án (`projects`).

Luồng tạo phiếu:

```text
Người dùng vào Xuất kho
  -> chọn ngày, kho, công trình, vật tư, số lượng, vị trí
  -> store() validate dữ liệu
  -> kiểm tra tồn hiện tại trong kho
  -> kiểm tra vật tư có trong định mức công trình hay không
  -> kiểm tra tổng đã xuất + đang chờ xuất không vượt định mức
  -> tạo inventory_exits với status = pending
  -> tạo inventory_exit_details
  -> chưa trừ tồn kho
```

Luồng duyệt phiếu:

```text
Admin tổng/Admin kho bấm duyệt
  -> approve()
  -> kiểm tra status = pending
  -> từng detail gọi InventoryService::updateStock(..., type = subtract)
  -> nếu không đủ tồn thì rollback
  -> nếu đủ tồn thì trừ material_warehouses.stock
  -> cập nhật status = completed
```

Luồng hủy phiếu:

```text
Nếu phiếu pending
  -> đổi status = cancelled

Nếu phiếu completed
  -> cộng lại số lượng đã xuất
  -> đổi status = cancelled
```

## 8. Luồng chuyển kho

Controller chính: `InventoryTransferController`.

Luồng tạo phiếu:

```text
Người dùng vào Chuyển kho
  -> chọn kho nguồn, kho đích, vật tư, số lượng
  -> store() validate kho nguồn khác kho đích
  -> kiểm tra tồn tại kho nguồn
  -> tạo inventory_transfers với status = pending
  -> tạo inventory_transfer_details
  -> chưa thay đổi tồn kho
```

Luồng duyệt phiếu:

```text
Admin tổng/Admin kho bấm duyệt
  -> approve()
  -> từng detail:
       trừ tồn ở kho nguồn
       cộng tồn ở kho đích
  -> cập nhật status = completed
```

Luồng hủy phiếu đã duyệt:

```text
Nếu completed:
  -> cộng lại tồn vào kho nguồn
  -> trừ lại tồn ở kho đích
  -> status = cancelled
```

## 9. Luồng kiểm kê kho

Controller chính: `InventoryCheckController`.

Luồng lập phiếu kiểm kê:

```text
Người dùng chọn kho
  -> hệ thống lấy danh sách material_warehouses của kho
  -> nhập số lượng thực tế
  -> store() tạo inventory_checks status = pending
  -> tạo inventory_check_details:
       system_stock
       actual_stock
       variance = actual_stock - system_stock
```

Luồng duyệt kiểm kê:

```text
Admin tổng/Admin kho duyệt
  -> approve()
  -> nếu variance > 0:
       tạo phiếu nhập điều chỉnh status = completed
       cộng tồn qua InventoryService
  -> nếu variance < 0:
       tạo phiếu xuất điều chỉnh status = completed
       trừ tồn qua InventoryService
  -> cập nhật inventory_checks.status = completed
```

Như vậy kiểm kê không sửa tồn trực tiếp theo kiểu set stock, mà tạo phiếu điều chỉnh nhập/xuất để giữ lịch sử biến động.

## 10. Luồng cảnh báo tồn kho

Cảnh báo nằm ở `inventory_alerts`.

Nguồn tạo cảnh báo là `InventoryService::updateStock()`:

```text
Sau mỗi lần cộng/trừ tồn
  -> tính tổng tồn của vật tư trên các kho
  -> nếu tổng tồn < mức tối thiểu
       tạo/cập nhật cảnh báo chưa xử lý
  -> nếu tổng tồn >= mức tối thiểu
       đánh dấu cảnh báo chưa xử lý thành đã xử lý
```

`InventoryAlertController@index` hiển thị danh sách cảnh báo.

`InventoryAlertController@resolve` cho phép Admin tổng/Admin kho đánh dấu cảnh báo là đã xử lý.

## 11. Luồng báo cáo và xuất file

Controller chính: `ReportController`.

Báo cáo tồn kho lấy dữ liệu từ `material_warehouses`, join sang:

- `materials`
- `warehouses`
- `units`

Luồng báo cáo:

```text
Người dùng mở Báo cáo tồn kho
  -> nếu Admin tổng: xem toàn bộ hoặc lọc theo kho
  -> nếu không phải Admin tổng: chỉ xem kho được gán
  -> trả view reports.inventory
```

Xuất file:

- Excel dùng `maatwebsite/excel`.
- PDF dùng `barryvdh/laravel-dompdf`.

Các màn hình nhập kho và xuất kho cũng có route xuất Excel/PDF riêng.

## 12. Luồng import/export vật tư

`MaterialController` hỗ trợ:

- `export()`: xuất danh sách vật tư.
- `downloadTemplate()`: tải file mẫu import vật tư.
- `import()`: nhập file xlsx/xls/csv.

`MaterialsImport` đọc các cột:

- `ten_vat_tu`
- `mo_ta`
- `don_vi_tinh_id`
- `ton_toi_thieu`
- `ton_toi_da`

Nếu có `id` và tìm thấy vật tư thì cập nhật. Nếu không có thì tạo vật tư mới.

## 13. Tóm tắt luồng nghiệp vụ chính

Luồng tổng quát của hệ thống:

```text
1. Admin cấu hình dữ liệu nền
   -> kho, user, đơn vị tính, nhóm vật tư, vật tư, nhà cung cấp, công trình

2. Admin cập nhật định mức vật tư cho công trình
   -> project_materials

3. Nhân sự kho tạo phiếu nghiệp vụ
   -> nhập kho / xuất kho / chuyển kho / kiểm kê
   -> phiếu ở trạng thái pending

4. Admin tổng hoặc Admin kho duyệt phiếu
   -> controller gọi InventoryService
   -> cập nhật material_warehouses
   -> phiếu chuyển sang completed

5. Nếu cần hủy phiếu đã duyệt
   -> hệ thống tạo thao tác đảo ngược tồn kho
   -> phiếu chuyển sang cancelled

6. Dashboard, báo cáo, cảnh báo
   -> đọc dữ liệu tồn từ material_warehouses và lịch sử phiếu
```

## 14. Các bảng quan trọng và quan hệ chính

```text
users
  -> belongsTo warehouses

warehouses
  -> hasMany users
  -> belongsToMany materials qua material_warehouses

materials
  -> belongsTo units
  -> belongsTo categories
  -> hasMany material_warehouses

suppliers
  -> hasMany inventory_entries

projects
  -> hasMany inventory_exits
  -> hasMany project_materials

inventory_entries
  -> belongsTo warehouse, supplier, user
  -> hasMany inventory_entry_details

inventory_exits
  -> belongsTo warehouse, project, user
  -> hasMany inventory_exit_details

inventory_transfers
  -> belongsTo fromWarehouse, toWarehouse, user
  -> hasMany inventory_transfer_details

inventory_checks
  -> belongsTo warehouse, user
  -> hasMany inventory_check_details
```

## 15. Điểm cần lưu ý khi đọc hoặc phát triển tiếp

- Tồn kho thực tế chỉ nên tin ở `material_warehouses`.
- Phiếu ở trạng thái `pending` chưa làm thay đổi tồn kho.
- Phiếu chỉ cập nhật tồn khi được duyệt.
- Các thao tác duyệt/hủy dùng transaction để rollback khi có lỗi.
- User không phải `Admin tổng` thường bị giới hạn theo `warehouse_id`.
- Một số cấu hình phân quyền nằm ở `config/permissions.php`, nhưng route hiện tại chủ yếu vẫn dùng middleware `role`.
- Source có model và migration cho `purchase_orders` và `material_batches`, nhưng luồng controller sử dụng chưa hoàn chỉnh trong route hiện tại.
- Có điểm bất nhất cần kiểm tra thêm: `InventoryService` đang đọc `$material->min_stock_level`, trong khi model/migration vật tư dùng field `min_stock`.
- Các controller nhập/xuất/chuyển có truyền `note` khi tạo phiếu, nhưng model `InventoryEntry`, `InventoryExit`, `InventoryTransfer` hiện chưa đưa `note` vào `$fillable`, nên ghi chú có thể không được lưu qua mass assignment.
