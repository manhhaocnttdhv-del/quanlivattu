# Hướng dẫn Bảo vệ Đồ án: Hệ thống Quản lý Vật tư

Tài liệu này giúp bạn nắm vững kiến thức về phần mềm để trả lời các câu hỏi của Giảng viên khi bảo vệ đồ án.

---

## 1. Giới thiệu Công nghệ (Tech Stack)
Khi được hỏi "Em dùng gì để làm đồ án này?", hãy trả lời:
-   **Backend**: Framework **Laravel** (PHP). Lý do: Bảo mật cao, hỗ trợ mô hình MVC rõ ràng, cộng đồng lớn.
-   **Frontend**: Blade Template (của Laravel), Bootstrap (CSS), JavaScript/jQuery.
-   **Database**: **MySQL**.
-   **Mô hình thiết kế**: **MVC** (Model - View - Controller) kết hợp với **Service Pattern** để xử lý logic nghiệp vụ tập trung.

---

## 2. Sơ đồ Thực thể Quan hệ (ERD) & Database
Bạn cần nhớ các bảng chính:
1.  **users**: Lưu thông tin người dùng và vai trò (role).
2.  **materials**: Danh mục vật tư (tên, đơn vị tính, mô tả).
3.  **warehouses**: Các kho hàng trong hệ thống.
4.  **inventories**: **Bảng quan trọng nhất** - Lưu số lượng tồn kho hiện tại của từng vật tư tại từng kho.
5.  **inventory_entries/exits**: Lưu thông tin chung của phiếu nhập/xuất (ai tạo, ngày nào, trạng thái).
6.  **inventory_details**: Lưu chi tiết từng vật tư trong phiếu đó.
7.  **stock_cards**: Lưu lịch sử biến động (Thẻ kho). Mỗi lần nhập/xuất thành công sẽ có 1 dòng ghi vào đây.

---

## 3. Giải thích Logic Nghiệp vụ (Core Logic)

### Câu hỏi: "Tại sao em không cộng tồn kho ngay khi nhân viên bấm 'Lưu phiếu'?"
**Trả lời:** 
-   Để đảm bảo quy trình kiểm soát chặt chẽ. Nhân viên chỉ là người nhập liệu (Draft). 
-   Phiếu phải được cấp quản lý (Admin) kiểm tra lại thông tin thực tế. 
-   Khi Admin bấm **Phê duyệt**, hệ thống mới thực hiện tính toán cộng/trừ vào bảng `inventories`. Điều này giúp tránh việc sai lệch dữ liệu do nhập nhầm.

### Câu hỏi: "Làm thế nào để em theo dõi được quá trình biến động của một loại vật tư?"
**Trả lời:** 
-   Em sử dụng bảng `stock_cards` (Thẻ kho). 
-   Mỗi hành động thay đổi số lượng (Nhập, Xuất, Điều chuyển) đều ghi lại: Số dư trước, Số lượng thay đổi, Số dư sau.
-   Giảng viên sẽ đánh giá cao phần này vì nó sát với thực tế kế toán.

### Câu hỏi: "Nếu xóa một phiếu đã hoàn thành thì sao?"
**Trả lời:** 
-   Hệ thống của em hạn chế việc xóa vĩnh viễn. Thay vào đó dùng trạng thái **Hủy (Cancel)**.
-   Khi hủy một phiếu đã duyệt, hệ thống sẽ thực hiện logic "Đảo ngược": Nếu là phiếu nhập thì trừ lại kho, nếu là phiếu xuất thì cộng lại kho, sau đó mới đổi trạng thái phiếu.

---

## 4. Các "Điểm cộng" kỹ thuật trong code
Nếu muốn khoe kỹ năng code, hãy chỉ vào các file sau:
1.  **app/Services/InventoryService.php**: Đây là nơi tập trung toàn bộ logic tính toán kho. Thay vì viết ở Controller, em viết ở Service để dễ tái sử dụng và bảo trì (Design Pattern).
2.  **DB::beginTransaction()**: Trong các hàm Approve/Cancel, em có sử dụng Transaction. Nếu một bước bị lỗi (ví dụ: đang cập nhật kho mà mất điện), toàn bộ các bước trước đó sẽ được quay xe (Rollback), không bao giờ có chuyện phiếu duyệt rồi mà kho chưa cộng.
3.  **Middleware 'role'**: Em tự viết Middleware để kiểm soát quyền. Ví dụ: Nhân viên không thể vào link dành cho Admin dù họ có biết URL.

---

## 5. Kịch bản Demo chuẩn
1.  Đăng nhập bằng **Nhân viên**.
2.  Tạo một **Phiếu nhập kho** (ví dụ nhập 100 cái bóng đèn). Show cho thầy cô thấy là Tồn kho lúc này vẫn chưa tăng.
3.  Đăng xuất, đăng nhập bằng **Admin**.
4.  Vào danh sách phiếu chờ duyệt, bấm **Phê duyệt**.
5.  Show bảng **Tồn kho** (đã tăng thêm 100) và **Thẻ kho** (đã có dòng lịch sử mới).
6.  Giải thích: "Đây là luồng nghiệp vụ hoàn chỉnh của hệ thống em."

---
*Chúc bạn bảo vệ đồ án thành công!*
