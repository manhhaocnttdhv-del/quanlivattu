# Tài liệu Mô tả Chi tiết Các chức năng Nâng cao (Phase 2)

Tài liệu này trình bày chi tiết về luồng nghiệp vụ và cách thức triển khai của 7 chức năng nâng cao sẽ được bổ sung vào hệ thống Quản lý Vật tư, với mục tiêu **"Làm hết và hạn chế tối đa Bug"**. 

Việc triển khai sẽ được chia thành từng giai đoạn (Module) để kiểm soát chất lượng code và đảm bảo luồng dữ liệu không bị xung đột.

---

## Mức độ ưu tiên và Kế hoạch triển khai (Roadmap)

Để hạn chế bug, chúng ta sẽ không làm đồng loạt tất cả cùng lúc mà chia thành 3 chặng (Sprint):
1.  **Sprint 1:** Cảnh báo tồn kho + Xuất file Excel nâng cao + Quy trình duyệt (Level 1).
2.  **Sprint 2:** Quản lý Đơn đặt hàng (PO) + Tính giá vốn hàng tồn kho.
3.  **Sprint 3:** Quản lý theo Lô/Hạn sử dụng + Quét mã vạch/QR Code.

---

## Chi tiết các chức năng

### 1. Cảnh báo tồn kho (Inventory Alerts)
**Mục tiêu:** Giúp thủ kho và quản lý biết được khi nào cần nhập thêm hàng.
*   **Luồng hoạt động:** 
    *   Thêm trường `min_stock_level` (Tồn kho tối thiểu) vào bảng `materials`.
    *   Mỗi khi có giao dịch xuất kho, hệ thống kích hoạt Observer/Event để kiểm tra tổng tồn kho hiện tại so với `min_stock_level`.
    *   Nếu tồn kho < mức tối thiểu, lưu 1 bản ghi cảnh báo vào bảng `alerts` hoặc gửi thông báo (Notifications).
*   **Giao diện:** Thêm một icon cái chuông 🔔 trên thanh điều hướng để hiển thị số lượng cảnh báo. Có một trang riêng liệt kê các mặt hàng "Sắp hết".

### 2. Xuất file Báo cáo nâng cao (Excel Export)
**Mục tiêu:** Cung cấp số liệu mềm cho Kế toán và Quản lý.
*   **Công nghệ sử dụng:** Cài đặt thư viện `maatwebsite/excel` cho Laravel.
*   **Luồng hoạt động:**
    *   Tại các trang danh sách (Vật tư, Nhập kho, Xuất kho, Báo cáo tồn kho), thêm nút "Xuất Excel".
    *   Cho phép lọc theo khoảng thời gian (Từ ngày - Đến ngày) trước khi xuất.
    *   File Excel được định dạng sẵn Header và Style cột số lượng, đơn giá rõ ràng.

### 3. Quy trình Phê duyệt (Approval Workflow)
**Mục tiêu:** Kiểm soát chặt chẽ việc xuất/nhập, tránh thất thoát.
*   **Luồng hoạt động:**
    *   Thêm cột `status` (Trạng thái) vào các bảng phiếu nhập (`inventory_entries`) và phiếu xuất (`inventory_exits`). 
    *   Các trạng thái: `pending` (Chờ duyệt), `approved` (Đã duyệt), `rejected` (Từ chối), `completed` (Hoàn thành).
    *   **Nhân viên kho:** Chỉ tạo phiếu, trạng thái mặc định là `pending`. Lúc này số lượng vật tư trong kho chưa bị thay đổi.
    *   **Quản lý (Có quyền duyệt):** Vào xem phiếu, nhấn "Duyệt". Khi chuyển sang `approved` thì hệ thống mới chạy logic cộng/trừ số lượng tồn kho.
*   **Chống Bug:** Sử dụng Database Transaction (DB::transaction) khi duyệt để đảm bảo việc đổi trạng thái và cộng/trừ số lượng xảy ra đồng thời. Nếu lỗi sẽ rollback toàn bộ.

### 4. Quản lý Đơn đặt hàng (Purchase Orders - PO)
**Mục tiêu:** Chuẩn hóa quy trình Mua hàng từ nhà cung cấp.
*   **Luồng hoạt động:**
    *   Tạo mới bảng `purchase_orders` và `purchase_order_details`.
    *   Bộ phận Mua hàng tạo PO gửi nhà cung cấp.
    *   Khi thủ kho lập Phiếu Nhập Kho, sẽ có tùy chọn "Nhập từ Đơn đặt hàng #...".
    *   Hệ thống tự động đổ danh sách vật tư từ PO sang Phiếu Nhập, thủ kho chỉ cần điền số lượng thực nhận. Nếu thực nhận đủ số lượng, PO chuyển sang trạng thái "Đã hoàn thành".

### 5. Quản lý theo Lô và Hạn sử dụng (Batch & Expiry Date)
**Mục tiêu:** Theo dõi được tuổi thọ của vật tư, tránh hỏng hóc.
*   **Luồng hoạt động:**
    *   Thay vì chỉ lưu tổng số lượng trên mỗi vật tư, hệ thống cần tách tồn kho theo từng bảng `material_batches` (Lô vật tư).
    *   Mỗi khi nhập hàng, thủ kho phải điền "Mã lô" và "Hạn sử dụng".
    *   Mỗi khi xuất hàng, hệ thống tự động gợi ý xuất từ Lô có hạn sử dụng gần nhất (FIFO/FEFO) để tránh hàng bị hết hạn.
*   **Chống Bug:** Đây là module phức tạp nhất. Cần viết Unit Test kỹ lưỡng logic trừ số lượng chéo qua nhiều lô (Ví dụ: Cần xuất 100, Lô A còn 40, Lô B còn 60 -> Phải trừ sạch Lô A và trừ 60 ở Lô B).

### 6. Tính giá vốn tồn kho (Cost Calculation)
**Mục tiêu:** Xác định giá trị tiền tệ của hàng tồn kho.
*   **Phương án:** Áp dụng phương pháp **Bình quân gia quyền (Weighted Average Cost)** vì dễ triển khai và phổ biến nhất.
*   **Luồng hoạt động:**
    *   Thêm trường `average_price` vào bảng vật tư.
    *   Công thức: Khi có Phiếu Nhập mới, `Giá bình quân mới = (Số lượng tồn cũ * Giá bình quân cũ + Số lượng nhập * Giá nhập) / (Số lượng tồn cũ + Số lượng nhập)`.
    *   Khi xuất kho, giá trị xuất kho sẽ lấy theo giá bình quân tại thời điểm đó.

### 7. Tích hợp Mã vạch/QR Code (Barcode/QR Code)
**Mục tiêu:** Thao tác nhập/xuất/kiểm kê nhanh, tránh chọn nhầm mã.
*   **Luồng hoạt động:**
    *   Sử dụng thư viện `milon/barcode` hoặc `simplesoftwareio/simple-qrcode`.
    *   Trong trang chi tiết Vật tư, hiển thị mã vạch.
    *   Trang Nhập/Xuất có ô input để focus. Khi dùng máy quét (hoạt động như bàn phím gõ nhanh + Enter), hệ thống tự động tìm và thêm vật tư đó vào lưới chi tiết phiếu.

---
**Cam kết phát triển:** 
Tất cả các tính năng sẽ được phát triển tuân thủ kiến trúc MVC của Laravel. Mọi thao tác làm thay đổi số liệu kho sẽ được đặt trong `DB::transaction()` và tạo Service Class riêng biệt để xử lý logic, tách biệt khỏi Controller nhằm giữ code gọn gàng, dễ bảo trì và hạn chế tối đa Bug/Sai lệch dữ liệu.
