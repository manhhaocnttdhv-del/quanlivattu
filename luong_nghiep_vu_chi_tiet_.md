# Luồng Nghiệp vụ Chi tiết - Hệ thống Quản lý Vật tư

Tài liệu này mô tả chi tiết luồng hoạt động của hệ thống từ lúc bắt đầu đến khi kết thúc cho từng vai trò người dùng (Role), dựa trên cấu trúc source code hiện tại.

---

## 1. Phân quyền & Vai trò (RBAC)

Hệ thống sử dụng cơ chế Middleware (`role`) để kiểm soát truy cập dựa trên cột `role` trong bảng `users`. Có 3 vai trò chính:
- **Admin tổng**: Toàn quyền hệ thống. Quản lý kho, nhân sự và cấu hình.
- **Admin kho**: Quản lý nghiệp vụ tại kho, phê duyệt các phiếu nhập/xuất/kiểm kê.
- **Nhân viên kho**: Thực hiện nhập liệu hàng ngày, tạo phiếu yêu cầu chờ duyệt.

---

## 2. Cấu trúc Thư mục Quan trọng (Project Structure)

Để hiểu mã nguồn nằm ở đâu, bạn cần chú ý các thư mục sau trong kiến trúc Laravel:

- **`app/Http/Controllers`**: Chứa các file điều khiển. Mỗi khi bạn bấm một nút trên web, Controller sẽ tiếp nhận và quyết định sẽ làm gì tiếp theo (ví dụ: `InventoryEntryController` xử lý phiếu nhập).
- **`app/Models`**: Chứa các "mẫu" đại diện cho các bảng trong cơ sở dữ liệu. Đây là nơi định nghĩa các mối quan hệ (ví dụ: Một vật tư thuộc về một Đơn vị tính).
- **`app/Services`**: Đây là thư mục quan trọng nhất của dự án này. Nó chứa `InventoryService.php` - nơi tập trung toàn bộ các thuật toán cộng/trừ tồn kho và ghi thẻ kho.
- **`database/migrations`**: Chứa các file định nghĩa cấu trúc bảng (cột nào là số, cột nào là chữ).
- **`resources/views`**: Chứa toàn bộ giao diện (HTML/Blade). Nếu muốn sửa giao diện trang nào, bạn tìm trong này.
- **`routes/web.php`**: File định nghĩa các đường dẫn (URL). Muốn biết một URL chạy vào Controller nào thì xem tại đây.
- **`public/`**: Chứa các file tĩnh như hình ảnh vật tư, file CSS và JavaScript.

---

## 3. Luồng chi tiết theo từng Vai trò

### A. Vai trò: Admin tổng (Super Admin)
*Người dùng có quyền cao nhất, quản trị toàn bộ hệ thống.*

1.  **Bắt đầu:** Đăng nhập vào hệ thống.
2.  **Quản trị Hệ thống:**
    *   Quản lý danh sách Người dùng (`UserController`).
    *   Thiết lập và phân quyền chức năng (`PermissionController`).
    *   Quản lý danh mục Kho bãi toàn hệ thống (`WarehouseController`).
3.  **Quản lý Danh mục (Master Data):**
    *   Thêm/Sửa/Xóa Vật tư, Đơn vị tính, Nhà cung cấp, Dự án.
    *   Import vật tư từ Excel, Export danh mục.
4.  **Giám sát & Phê duyệt:**
    *   Xem tất cả các phiếu Nhập/Xuất/Chuyển kho của tất cả các kho.
    *   **Phê duyệt (Approve):** Chuyển trạng thái phiếu từ `pending` sang `completed`. Tại bước này, hệ thống mới chính thức cập nhật số lượng tồn kho thực tế thông qua `InventoryService`.
    *   **Hủy (Cancel):** Hủy phiếu nếu có sai sót. Nếu phiếu đã hoàn thành, hệ thống sẽ tự động hoàn lại (reverse) số lượng tồn kho.
5.  **Kết thúc:** Xem báo cáo tổng hợp tồn kho toàn hệ thống và Đăng xuất.

---

### B. Vai trò: Admin kho (Warehouse Manager)
*Quản lý trực tiếp các hoạt động tại kho được phân công.*

1.  **Bắt đầu:** Đăng nhập vào hệ thống.
2.  **Quản lý Nghiệp vụ:**
    *   Quản lý danh mục Vật tư, Nhà cung cấp, Dự án (giới hạn trong phạm vi nghiệp vụ kho).
3.  **Kiểm soát Giao dịch:**
    *   Kiểm tra các phiếu do Nhân viên kho tạo (`pending`).
    *   Thực hiện **Phê duyệt** hoặc **Hủy** phiếu Nhập/Xuất/Chuyển kho/Kiểm kê của kho mình phụ trách.
4.  **Xử lý Cảnh báo:**
    *   Theo dõi và xử lý các cảnh báo tồn kho thấp (`InventoryAlertController`).
5.  **Kết thúc:** Xuất báo cáo Excel/PDF cho kho quản lý và Đăng xuất.

---

### C. Vai trò: Nhân viên kho (Warehouse Staff)
*Người thực hiện các thao tác nhập liệu hàng ngày.*

1.  **Bắt đầu:** Đăng nhập vào hệ thống.
2.  **Thực hiện Giao dịch:**
    *   **Tạo Phiếu Nhập kho:** Điền thông tin nhà cung cấp, vật tư, số lượng, đơn giá. Phiếu mặc định ở trạng thái `pending`.
    *   **Tạo Phiếu Xuất kho:** Chọn vật tư từ kho hiện tại để xuất cho dự án.
    *   **Tạo Phiếu Luân chuyển:** Đề xuất chuyển hàng giữa các kho.
    *   **Tạo Phiếu Kiểm kê:** Ghi nhận số lượng thực tế tại kho.
3.  **Theo dõi trạng thái:**
    *   Xem danh sách phiếu mình đã tạo và chờ Admin phê duyệt.
    *   Nhân viên **không có quyền** Phê duyệt hoặc Hủy phiếu đã hoàn thành.
4.  **Tra cứu:**
    *   Xem danh mục vật tư, đơn vị tính và báo cáo tồn kho hiện tại.
5.  **Kết thúc:** Hoàn tất nhập liệu và Đăng xuất.

---

## 4. Cấu trúc Cơ sở dữ liệu (Database Tables)

Hệ thống được thiết kế với các bảng chính sau đây để quản lý luồng dữ liệu:

### A. Nhóm Quản trị & Danh mục (Master Data)
- **users**: Lưu thông tin tài khoản, mật khẩu, vai trò (`role`) và kho quản lý (`warehouse_id`).
- **materials**: Danh mục vật tư (tên, mã, quy cách, hình ảnh).
- **units**: Quản lý các đơn vị tính (Cái, Bộ, Kg, Mét...).
- **suppliers**: Thông tin các nhà cung cấp vật tư.
- **warehouses**: Danh sách các kho hàng trong hệ thống.
- **projects**: Thông tin các dự án/công trình (nơi tiếp nhận vật tư xuất kho).
- **role_permissions**: Lưu cấu hình phân quyền chi tiết cho từng vai trò người dùng.

### B. Nhóm Tồn kho & Giao dịch (Inventory & Transactions)
- **inventories**: **Bảng quan trọng nhất**, lưu số lượng tồn kho thực tế của từng vật tư tại từng kho.
- **inventory_entries / inventory_entry_details**: Lưu thông tin đầu phiếu và chi tiết vật tư của các giao dịch **Nhập kho**.
- **inventory_exits / inventory_exit_details**: Lưu thông tin đầu phiếu và chi tiết vật tư của các giao dịch **Xuất kho**.
- **inventory_transfers / inventory_transfer_details**: Lưu thông tin các giao dịch **Luân chuyển** hàng hóa giữa các kho.
- **inventory_checks / inventory_check_details**: Lưu dữ liệu các đợt **Kiểm kê**, dùng để điều chỉnh số lượng tồn kho thực tế so với sổ sách.
- **stock_cards**: **Thẻ kho**, ghi lại nhật ký mọi biến động (nhập, xuất, chuyển) của từng vật tư. Đây là bảng dùng để truy xuất nguồn gốc và làm báo cáo lịch sử.
- **inventory_alerts**: Lưu các cảnh báo khi vật tư trong kho xuống dưới mức tồn tối thiểu.

---

## 5. Luồng xử lý Logic trong Source Code (Ví dụ: Nhập kho)

Dưới đây là trình tự code thực thi khi một phiếu Nhập kho được xử lý:

1.  **Tạo phiếu (`InventoryEntryController@store`):**
    *   Validate dữ liệu đầu vào.
    *   Lưu vào bảng `inventory_entries` với `status = 'pending'`.
    *   Lưu chi tiết vào bảng `inventory_entry_details`.
    *   *Lúc này tồn kho chưa thay đổi.*

2.  **Phê duyệt (`InventoryEntryController@approve`):**
    *   Middleware kiểm tra role (phải là `Admin tổng` hoặc `Admin kho`).
    *   Gọi `InventoryService@updateStock`.
    *   `InventoryService` thực hiện:
        *   Cập nhật số lượng trong bảng `inventories`.
        *   Ghi nhận lịch sử vào bảng `stock_cards` (Thẻ kho).
    *   Cập nhật `inventory_entries.status = 'completed'`.

3.  **Hủy phiếu (`InventoryEntryController@cancel`):**
    *   Nếu phiếu đang `pending`: Chuyển trạng thái sang `cancelled`.
    *   Nếu phiếu đã `completed`: Gọi `updateStock` với hành động `subtract` để hoàn trả số lượng, sau đó mới đổi trạng thái.

---

## 6. Các file quan trọng cần lưu ý

- **Routes:** `routes/web.php` (Phân quyền middleware `role`).
- **Models:** `app/Models/User.php` (Logic kiểm tra role: `isAdminTong()`, `isAdminKho()`).
- **Services:** `app/Services/InventoryService.php` (Xử lý tăng/giảm tồn kho và Thẻ kho).
- **Controllers:** Các controller quản lý giao dịch kho (`InventoryEntryController`, `InventoryExitController`,...).
