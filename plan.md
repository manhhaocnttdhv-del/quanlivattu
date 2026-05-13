# Tổng hợp chức năng hệ thống Quản lý Vật tư

Dựa vào cấu trúc mã nguồn Laravel trong thư mục `quanlivattu`, dưới đây là danh sách các chức năng chính của hệ thống phần mềm này:

## 1. Quản lý Danh mục (Catalog Management)
*   **Quản lý Vật tư (Material):** Thêm mới, chỉnh sửa, xóa và theo dõi danh sách các loại vật tư, thiết bị.
*   **Quản lý Đơn vị tính (Unit):** Định nghĩa các đơn vị tính (cái, chiếc, kg, mét,...) cho vật tư.
*   **Quản lý Nhà cung cấp (Supplier):** Quản lý thông tin đối tác cung cấp vật tư cho hệ thống.
*   **Quản lý Kho bãi (Warehouse):** Thiết lập và quản lý danh sách các kho chứa hàng.
*   **Quản lý Dự án/Công trình (Project):** Quản lý thông tin các công trình, dự án để phục vụ cho việc xuất vật tư đến đúng nơi.

## 2. Quản lý Giao dịch Kho (Inventory Transactions)
*   **Nhập kho (Inventory Entry):** Tạo và quản lý phiếu nhập kho khi mua vật tư từ nhà cung cấp.
*   **Xuất kho (Inventory Exit):** Tạo phiếu xuất kho vật tư cho các dự án/công trình. Hệ thống có tích hợp chức năng xuất/in phiếu PDF (ví dụ file `inventory_exits_pdf.blade.php`).
*   **Luân chuyển kho (Inventory Transfer):** Quản lý việc điều chuyển vật tư qua lại giữa các kho bãi trong nội bộ.
*   **Kiểm kê kho (Inventory Check):** Tạo các phiếu kiểm kê định kỳ để đối chiếu số lượng thực tế so với sổ sách và điều chỉnh nếu có sai lệch.

## 3. Báo cáo & Thống kê (Reports & Statistics)
*   **Bảng điều khiển (Dashboard):** Xem tổng quan (dashboard) về tình hình tồn kho, số lượng nhập/xuất trong kỳ.
*   **Báo cáo (Report):** Tổng hợp dữ liệu báo cáo xuất - nhập - tồn kho.
*   **Thẻ kho (Stock Card):** Theo dõi lịch sử biến động (nhập, xuất) chi tiết của từng loại vật tư theo thời gian thực.

## 4. Quản trị Hệ thống (System & Admin)
*   **Quản lý Người dùng (User):** Thêm, sửa, xóa tài khoản sử dụng hệ thống.
*   **Phân quyền (Permission & Role):** Phân quyền truy cập chức năng chi tiết cho từng người dùng/nhóm người dùng (tích hợp role-permission).
*   **Xác thực (Auth):** Chức năng đăng nhập, đăng xuất hệ thống.

---

## 5. Các chức năng nâng cao (Chuẩn bị phát triển - Phase 2)
Để phần mềm hoàn thiện và chuyên nghiệp hơn, các chức năng sau sẽ được bổ sung:

*   **Cảnh báo tồn kho (Inventory Alerts):** Cảnh báo tự động khi số lượng tồn kho giảm dưới mức tối thiểu hoặc vật tư ứ đọng lâu ngày.
*   **Quy trình Phê duyệt (Approval Workflow):** Yêu cầu cấp quản lý duyệt Phiếu Xuất/Nhập/Luân chuyển và "Yêu cầu cấp vật tư" trước khi thực hiện.
*   **Quản lý Đơn đặt hàng (Purchase Orders - PO):** Lên đơn đặt hàng từ nhà cung cấp và đối chiếu khi nhập kho.
*   **Tích hợp Mã vạch/QR Code:** Sinh mã QR/Barcode cho từng sản phẩm phục vụ quét xuất/nhập/kiểm kê.
*   **Quản lý theo Lô và Hạn sử dụng (Batch & Expiry Date):** Quản lý vật tư có hạn sử dụng (hóa chất, sơn,...) và xuất kho theo tiêu chuẩn FIFO.
*   **Tính giá vốn tồn kho (Cost Calculation):** Tính giá trị kho tự động theo Bình quân gia quyền, FIFO hoặc Giá đích danh.
*   **Xuất file Báo cáo nâng cao:** Hỗ trợ xuất dữ liệu ra Excel (.xlsx) cho tất cả các bảng dữ liệu và báo cáo thống kê.
