# Chương 3. Giao diện và Kiểm thử hệ thống

## 3.1. Giao diện quản lý Đơn vị tính

Giao diện quản lý đơn vị tính hiển thị danh sách các đơn vị tính hiện có trong hệ thống dưới dạng bảng với các cột: **STT** và **Tên đơn vị tính**. Các thông tin này được lưu trữ trong bảng `units` của cơ sở dữ liệu.

Chỉ **Admin tổng** và **Admin kho** mới thấy nút **"Thêm mới"** và các nút thao tác. Khi nhấn "Thêm mới", hệ thống điều hướng đến form nhập tên đơn vị tính. Sau khi lưu, danh sách tự động cập nhật. Mỗi dòng trong bảng có nút **"Sửa"** (màu vàng) và **"Xóa"** (màu đỏ kèm xác nhận trước khi xóa).

*Hình 3.1. Giao diện quản lý Đơn vị tính*

### 3.1.1. Kiểm thử chức năng quản lý Đơn vị tính

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Thêm đơn vị tính | Kiểm tra thêm đơn vị tính mới với tên hợp lệ | Đơn vị tính mới được thêm vào danh sách; nếu để trống tên sẽ thông báo lỗi và không thể thêm |
| Sửa đơn vị tính | Kiểm tra cập nhật tên đơn vị tính | Tên đơn vị tính được cập nhật và hiển thị ngay trên danh sách |
| Xóa đơn vị tính | Kiểm tra xóa đơn vị tính không còn sử dụng | Hệ thống hiển thị hộp thoại xác nhận; sau khi xác nhận, đơn vị tính bị xóa khỏi danh sách |
| Danh sách đơn vị tính | Kiểm tra hiển thị toàn bộ danh sách | Danh sách hiển thị đầy đủ, phân trang hoạt động đúng |
| Phân quyền | Kiểm tra hiển thị nút thao tác theo vai trò | Admin tổng/Admin kho thấy nút Thêm/Sửa/Xóa; Nhân viên kho chỉ xem danh sách |

*Bảng 3.1. Kiểm thử giao diện quản lý Đơn vị tính*

---

## 3.2. Giao diện quản lý Nhà cung cấp

Giao diện quản lý nhà cung cấp hiển thị danh sách các nhà cung cấp với các cột: **STT**, **Tên nhà cung cấp**, **Số điện thoại**, **Địa chỉ** và cột **Thao tác** (chỉ hiển thị với Admin tổng/Admin kho).

Chỉ **Admin tổng** và **Admin kho** mới thấy nút **"Thêm mới"**. Khi nhấn, hệ thống chuyển đến form nhập thông tin nhà cung cấp gồm: tên, số điện thoại, địa chỉ. Mỗi dòng có nút **"Sửa"** và **"Xóa"** kèm xác nhận.

*Hình 3.2. Giao diện quản lý Nhà cung cấp*

### 3.2.1. Kiểm thử chức năng quản lý Nhà cung cấp

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Thêm nhà cung cấp | Kiểm tra thêm mới với đầy đủ thông tin | Nhà cung cấp mới xuất hiện trong danh sách; nếu thiếu tên sẽ thông báo lỗi |
| Sửa nhà cung cấp | Kiểm tra cập nhật tên, SĐT, địa chỉ | Thông tin nhà cung cấp được cập nhật đúng |
| Xóa nhà cung cấp | Kiểm tra xóa nhà cung cấp | Hệ thống xác nhận trước khi xóa; sau xác nhận xóa khỏi danh sách |
| Danh sách nhà cung cấp | Kiểm tra hiển thị danh sách | Hiển thị đầy đủ STT, tên, SĐT, địa chỉ; phân trang hoạt động đúng |
| Phân quyền | Kiểm tra hiển thị theo vai trò | Admin tổng/Admin kho thấy nút thao tác; Nhân viên kho chỉ xem |

*Bảng 3.2. Kiểm thử giao diện quản lý Nhà cung cấp*

---

## 3.3. Giao diện quản lý Nhóm vật tư (Danh mục)

Giao diện quản lý nhóm vật tư hiển thị danh sách phân loại vật tư với các cột: **STT**, **Tên nhóm**, **Mô tả**, **Nhóm con** (hiển thị các nhãn badge cho danh mục con) và **Số vật tư** (số lượng vật tư thuộc nhóm đó kể cả nhóm con).

Chỉ **Admin tổng** và **Admin kho** mới thấy nút **"Thêm nhóm"** và các nút thao tác. Form thêm/sửa cho phép nhập tên nhóm, mô tả và chọn nhóm cha (hỗ trợ cấu trúc danh mục cha – con). Khi xóa nhóm, hệ thống cảnh báo sẽ xóa cả nhóm con đi kèm.

*Hình 3.3. Giao diện quản lý Nhóm vật tư*

### 3.3.1. Kiểm thử chức năng quản lý Nhóm vật tư

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Thêm nhóm vật tư | Kiểm tra thêm nhóm mới, có thể chọn nhóm cha | Nhóm mới được thêm vào danh sách; badge nhóm con hiển thị đúng |
| Sửa nhóm vật tư | Kiểm tra cập nhật tên, mô tả, nhóm cha | Thông tin nhóm vật tư được cập nhật |
| Xóa nhóm vật tư | Kiểm tra xóa nhóm kèm cảnh báo nhóm con | Hệ thống hiện thông báo "Xóa nhóm này và tất cả nhóm con?"; sau xác nhận xóa khỏi danh sách |
| Danh sách nhóm vật tư | Kiểm tra hiển thị nhóm cha và nhóm con | Danh sách hiển thị đúng số vật tư, nhóm con dạng badge |
| Phân quyền | Kiểm tra hiển thị theo vai trò | Admin tổng/Admin kho thấy nút thao tác; Nhân viên kho chỉ xem |

*Bảng 3.3. Kiểm thử giao diện quản lý Nhóm vật tư*

---

## 3.4. Giao diện quản lý Vật tư

Giao diện quản lý vật tư hiển thị danh sách vật tư với các cột: **STT**, **Tên vật tư**, **Nhóm** (badge màu xám hiển thị full path danh mục), **Đơn vị tính**, **Mô tả**, **Tồn tối thiểu**, **Tồn tối đa** và cột **Thao tác**.

Bộ lọc tìm kiếm gồm 4 tiêu chí: tìm theo **tên vật tư** (ô text), **đơn vị tính** (dropdown), **nhóm vật tư** (dropdown hỗ trợ nhóm con có thụt lề), **trạng thái tồn kho** (Dưới tối thiểu / Trên tối đa). Chỉ **Admin tổng** và **Admin kho** thấy các nút **"Tải file mẫu"**, **"Import"** (upload Excel), **"Export"** và **"Thêm mới"**.

*Hình 3.4. Giao diện quản lý Vật tư*

### 3.4.1. Kiểm thử chức năng quản lý Vật tư

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Thêm vật tư | Kiểm tra thêm vật tư mới với đầy đủ thông tin (tên, nhóm, ĐVT, tồn min/max) | Vật tư mới xuất hiện trong danh sách; thiếu tên thông báo lỗi |
| Sửa vật tư | Kiểm tra cập nhật thông tin vật tư | Thông tin vật tư được cập nhật đúng |
| Xóa vật tư | Kiểm tra xóa vật tư không còn sử dụng | Hộp thoại xác nhận xuất hiện; sau xác nhận vật tư bị xóa khỏi danh sách |
| Tìm kiếm/lọc | Kiểm tra lọc theo tên, ĐVT, nhóm, trạng thái tồn | Danh sách lọc đúng theo từng tiêu chí; nút "Xóa lọc" trả về toàn bộ danh sách |
| Import Excel | Kiểm tra nhập dữ liệu hàng loạt từ file Excel | File hợp lệ: dữ liệu được import thành công; file sai định dạng: thông báo lỗi |
| Export Excel | Kiểm tra xuất danh sách vật tư ra file Excel | File Excel được tải xuống với đầy đủ dữ liệu |
| Phân quyền | Kiểm tra hiển thị nút thao tác theo vai trò | Admin tổng/Admin kho thấy đầy đủ nút; Nhân viên kho chỉ xem danh sách |

*Bảng 3.4. Kiểm thử giao diện quản lý Vật tư*

---

## 3.5. Giao diện quản lý Kho hàng

Giao diện quản lý kho hàng hiển thị danh sách các kho với các cột: **STT**, **Tên kho**, **Địa chỉ**, **Quản lý kho** (tên người phụ trách), **Trạng thái** (badge xanh "Đang hoạt động" / xám "Ngừng hoạt động") và cột **Thao tác**.

Chỉ **Admin tổng** mới thấy nút **"Thêm mới"** và các nút thao tác (Sửa/Xóa). Form thêm/sửa cho phép nhập tên kho, địa chỉ, chọn người quản lý kho và trạng thái. Khi xóa kho, hệ thống yêu cầu xác nhận trước khi thực hiện.

*Hình 3.5. Giao diện quản lý Kho hàng*

### 3.5.1. Kiểm thử chức năng quản lý Kho hàng

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Thêm kho | Kiểm tra thêm kho mới với tên, địa chỉ, người quản lý | Kho mới xuất hiện trong danh sách với trạng thái "Đang hoạt động" |
| Sửa kho | Kiểm tra cập nhật tên, địa chỉ, người quản lý, trạng thái | Thông tin kho được cập nhật đúng |
| Xóa kho | Kiểm tra xóa kho | Hộp thoại xác nhận xuất hiện; sau xác nhận kho bị xóa khỏi danh sách |
| Danh sách kho | Kiểm tra hiển thị danh sách | Hiển thị đúng tên, địa chỉ, quản lý kho, badge trạng thái |
| Phân quyền | Kiểm tra chỉ Admin tổng thấy nút thao tác | Admin kho và Nhân viên kho chỉ xem danh sách, không thấy nút Thêm/Sửa/Xóa |

*Bảng 3.5. Kiểm thử giao diện quản lý Kho hàng*

---

## 3.6. Giao diện quản lý Phiếu nhập kho

Giao diện quản lý phiếu nhập kho hiển thị danh sách các phiếu nhập với các cột: **ID**, **Ngày nhập**, **Kho hàng**, **Nhà cung cấp**, **Người lập**, **Trạng thái** (badge: vàng "Chờ xử lý" / xanh "Hoàn thành" / xám "Đã hủy") và cột **Thao tác**.

Bộ lọc gồm: **Từ ngày**, **Đến ngày**, **Trạng thái**, **Kho** (chỉ Admin tổng thấy), **Nhà cung cấp**. Mọi người dùng đều thấy nút **"Lập phiếu nhập"**. Admin tổng/Admin kho thấy thêm nút **"Xuất Excel"** và **"Xuất PDF"**. Mỗi dòng có nút **"Xem"** (chi tiết). Phiếu ở trạng thái *Chờ xử lý*: Admin thấy nút **"Duyệt"** (cộng tồn kho) và **"Hủy"**. Phiếu *Hoàn thành*: Admin thấy nút **"Hủy"** kèm cảnh báo sẽ trừ lại tồn kho.

*Hình 3.6. Giao diện quản lý Phiếu nhập kho*

### 3.6.1. Kiểm thử chức năng quản lý Phiếu nhập kho

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Lập phiếu nhập | Kiểm tra tạo phiếu nhập với kho, nhà cung cấp và chi tiết vật tư | Phiếu được tạo với trạng thái "Chờ xử lý"; xuất hiện trong danh sách |
| Duyệt phiếu | Kiểm tra duyệt phiếu và cộng tồn kho | Sau duyệt: trạng thái → "Hoàn thành"; tồn kho vật tư tương ứng tăng đúng số lượng |
| Hủy phiếu chờ | Kiểm tra hủy phiếu đang chờ duyệt | Phiếu chuyển trạng thái "Đã hủy"; tồn kho không thay đổi |
| Hủy phiếu đã duyệt | Kiểm tra hủy phiếu đã hoàn thành | Cảnh báo rõ ràng sẽ trừ lại tồn kho; sau xác nhận tồn kho giảm tương ứng |
| Lọc phiếu | Kiểm tra lọc theo ngày, trạng thái, kho, nhà cung cấp | Danh sách lọc đúng theo từng tiêu chí |
| Xuất Excel/PDF | Kiểm tra xuất danh sách phiếu nhập | File được tải xuống với đầy đủ dữ liệu |
| Phân quyền | Kiểm tra hiển thị nút thao tác theo vai trò | Admin tổng/Admin kho thấy nút Duyệt/Hủy; Nhân viên kho chỉ thấy nút Xem |

*Bảng 3.6. Kiểm thử giao diện quản lý Phiếu nhập kho*

---

## 3.7. Giao diện quản lý Phiếu xuất kho

Giao diện quản lý phiếu xuất kho hiển thị danh sách các phiếu xuất với các cột: **ID**, **Ngày xuất**, **Kho xuất**, **Công trình** (dự án nhận vật tư), **Người lập**, **Trạng thái** và cột **Thao tác**.

Bộ lọc gồm: **Từ ngày**, **Đến ngày**, **Trạng thái**, **Kho** (chỉ Admin tổng thấy), **Công trình**. Nút **"Lập phiếu xuất"** hiển thị cho tất cả. Admin tổng/Admin kho thấy thêm **"Xuất Excel"** và **"Xuất PDF"**. Logic duyệt/hủy tương tự phiếu nhập: duyệt phiếu sẽ **trừ** tồn kho; hủy phiếu đã duyệt sẽ **trả lại** tồn kho.

*Hình 3.7. Giao diện quản lý Phiếu xuất kho*

### 3.7.1. Kiểm thử chức năng quản lý Phiếu xuất kho

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Lập phiếu xuất | Kiểm tra tạo phiếu xuất với kho, công trình và chi tiết vật tư | Phiếu được tạo với trạng thái "Chờ xử lý" |
| Duyệt phiếu | Kiểm tra duyệt phiếu và trừ tồn kho | Sau duyệt: trạng thái → "Hoàn thành"; tồn kho vật tư giảm đúng số lượng |
| Hủy phiếu đã duyệt | Kiểm tra hủy phiếu đã hoàn thành | Cảnh báo sẽ trả lại tồn kho; sau xác nhận tồn kho tăng lại tương ứng |
| Lọc phiếu | Kiểm tra lọc theo ngày, trạng thái, kho, công trình | Danh sách lọc đúng theo từng tiêu chí |
| Phân quyền | Kiểm tra hiển thị theo vai trò | Admin tổng/Admin kho thấy nút Duyệt/Hủy; Nhân viên kho chỉ thấy nút Xem |

*Bảng 3.7. Kiểm thử giao diện quản lý Phiếu xuất kho*

---

## 3.8. Giao diện quản lý Phiếu kiểm kê

Giao diện quản lý phiếu kiểm kê hiển thị danh sách các phiếu kiểm kê với các cột: **ID**, **Ngày kiểm kê**, **Kho hàng**, **Người lập**, **Trạng thái** (badge: vàng "Chờ xử lý" / xanh "Đã xử lý") và cột **Thao tác**.

Nút **"Tạo phiếu kiểm kê"** hiển thị cho tất cả người dùng. Mỗi dòng có nút **"Xem"** (chi tiết). Với phiếu đang *Chờ xử lý*, Admin tổng/Admin kho thấy nút **"Duyệt"** — khi duyệt hệ thống tự động điều chỉnh kho bằng cách sinh phiếu nhập/xuất bù trừ — và nút **"Hủy"**. Form tạo phiếu kiểm kê hiển thị toàn bộ danh sách vật tư trong kho kèm cột số lượng tồn hệ thống và ô nhập số lượng thực tế.

*Hình 3.8. Giao diện quản lý Phiếu kiểm kê*

### 3.8.1. Kiểm thử chức năng quản lý Phiếu kiểm kê

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Tạo phiếu kiểm kê | Kiểm tra tạo phiếu, chọn kho, nhập số lượng thực tế | Phiếu được tạo với trạng thái "Chờ xử lý"; danh sách vật tư hiển thị đúng |
| Xem chi tiết | Kiểm tra hiển thị số lượng hệ thống, thực tế và chênh lệch | Chênh lệch = thực tế - hệ thống, hiển thị đúng cho từng vật tư |
| Duyệt phiếu | Kiểm tra duyệt và tự động điều chỉnh tồn kho | Hệ thống tự sinh phiếu nhập/xuất bù trừ; trạng thái → "Đã xử lý"; tồn kho cập nhật đúng |
| Hủy phiếu | Kiểm tra hủy phiếu kiểm kê | Phiếu bị hủy, tồn kho không thay đổi |
| Phân quyền | Kiểm tra hiển thị nút thao tác theo vai trò | Admin tổng/Admin kho thấy nút Duyệt/Hủy; Nhân viên kho chỉ thấy nút Xem |

*Bảng 3.8. Kiểm thử giao diện quản lý Phiếu kiểm kê*

---

## 3.9. Giao diện Cảnh báo tồn kho

Giao diện cảnh báo tồn kho hiển thị danh sách các vật tư có số lượng dưới mức an toàn với các cột: **Mã vật tư**, **Tên vật tư**, **Tồn tối thiểu**, **Tồn thực tế**, **Trạng thái** (badge: vàng "Cần nhập hàng" / xanh "Đã xử lý"), **Ngày cảnh báo** và cột **Hành động**.

Cảnh báo được hệ thống **tự động sinh ra** khi tồn kho giảm xuống dưới `min_stock_level` sau mỗi giao dịch xuất kho hoặc sau khi duyệt phiếu kiểm kê. Tại cột hành động, với các cảnh báo chưa xử lý sẽ hiển thị nút **"Xử lý"** — khi nhấn, hộp thoại xác nhận xuất hiện "Xác nhận đã xử lý/đặt hàng cho vật tư này?"; sau xác nhận, trạng thái chuyển sang "Đã xử lý".

*Hình 3.9. Giao diện Cảnh báo tồn kho*

### 3.9.1. Kiểm thử chức năng Cảnh báo tồn kho

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Tự động tạo cảnh báo | Kiểm tra cảnh báo xuất hiện khi tồn kho < tồn tối thiểu | Sau khi duyệt phiếu xuất làm tồn kho giảm dưới min: cảnh báo tự động xuất hiện với trạng thái "Cần nhập hàng" |
| Hiển thị danh sách | Kiểm tra hiển thị đúng mã vật tư, tên, tồn tối thiểu, tồn thực tế | Dữ liệu hiển thị đúng, phân trang hoạt động đúng |
| Xử lý cảnh báo | Kiểm tra đánh dấu đã xử lý | Hộp thoại xác nhận xuất hiện; sau xác nhận badge chuyển sang "Đã xử lý", nút "Xử lý" biến mất |
| Không tạo cảnh báo trùng | Kiểm tra hệ thống không sinh cảnh báo mới khi đã có cảnh báo chưa xử lý | Không có cảnh báo trùng lặp cho cùng một vật tư |

*Bảng 3.9. Kiểm thử giao diện Cảnh báo tồn kho*

---

## 3.10. Giao diện quản lý Người dùng

Giao diện quản lý người dùng hiển thị danh sách tài khoản với các cột: **STT**, **Họ và tên**, **Email**, **Vai trò** (badge: đỏ "Admin tổng" / vàng "Admin kho" / xanh nhạt "Nhân viên kho"), **Kho quản lý/làm việc**, **Trạng thái** (badge: xanh "Đang hoạt động" / xám "Đã khóa") và cột **Thao tác**.

Chỉ **Admin tổng** mới có quyền truy cập trang này. Nút **"Thêm mới"** dẫn đến form nhập họ tên, email, vai trò, kho phụ trách, mật khẩu. Mỗi dòng có nút **"Sửa"** (chỉnh sửa thông tin, đổi vai trò, khóa/mở khóa) và **"Xóa"** (nút Xóa bị vô hiệu hóa với tài khoản đang đăng nhập).

*Hình 3.10. Giao diện quản lý Người dùng*

### 3.10.1. Kiểm thử chức năng quản lý Người dùng

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Thêm người dùng | Kiểm tra tạo tài khoản mới với email chưa tồn tại | Tài khoản mới xuất hiện trong danh sách với vai trò và kho đúng |
| Sửa thông tin | Kiểm tra cập nhật họ tên, email, vai trò, kho, trạng thái | Thông tin được cập nhật; badge vai trò và trạng thái hiển thị đúng |
| Khóa tài khoản | Kiểm tra khóa tài khoản nhân viên | Badge trạng thái chuyển sang "Đã khóa"; nhân viên không thể đăng nhập |
| Xóa người dùng | Kiểm tra xóa tài khoản nhân viên | Hộp thoại xác nhận xuất hiện; tài khoản bị xóa khỏi danh sách |
| Bảo vệ tài khoản đang dùng | Kiểm tra không thể tự xóa tài khoản đang đăng nhập | Nút "Xóa" bị vô hiệu hóa (disabled) với tài khoản đang đăng nhập |
| Phân quyền | Kiểm tra chỉ Admin tổng truy cập được trang | Admin kho và Nhân viên kho không thấy mục quản lý người dùng |

*Bảng 3.10. Kiểm thử giao diện quản lý Người dùng*

---

## 3.11. Giao diện Báo cáo Tồn kho

Giao diện báo cáo tồn kho hiển thị bảng số liệu tồn kho hiện tại với các cột: **STT**, **Kho hàng**, **Tên vật tư**, **ĐVT** (đơn vị tính), **Vị trí** (badge xám), **Tồn hiện tại** (màu xanh, font lớn), **Giá vốn** (bình quân), **Giá trị tồn** (= tồn × giá vốn). Hàng cuối bảng hiển thị **Tổng giá trị tài sản kho** (màu đỏ, font lớn).

Chỉ **Admin tổng** thấy dropdown chọn kho để lọc báo cáo theo từng kho cụ thể. Các nút thao tác gồm: **"Xuất Excel"**, **"Xuất PDF"** và **"In báo cáo"** (giao diện tối ưu cho in ấn với các thành phần điều hướng ẩn đi).

*Hình 3.11. Giao diện Báo cáo Tồn kho*

### 3.11.1. Kiểm thử chức năng Báo cáo Tồn kho

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Hiển thị tồn kho | Kiểm tra hiển thị đúng số lượng tồn và giá trị tồn | Số liệu khớp với dữ liệu sau các giao dịch nhập/xuất đã duyệt |
| Lọc theo kho | Kiểm tra lọc báo cáo theo từng kho (Admin tổng) | Báo cáo chỉ hiển thị vật tư thuộc kho được chọn |
| Tổng giá trị | Kiểm tra tính đúng tổng giá trị tài sản kho | Tổng = Σ(tồn × giá vốn bình quân) cho tất cả vật tư |
| Xuất Excel | Kiểm tra xuất báo cáo ra file Excel | File Excel được tải xuống với đầy đủ cột và dữ liệu |
| Xuất PDF | Kiểm tra xuất báo cáo ra file PDF | File PDF được tải xuống với định dạng đúng |
| In báo cáo | Kiểm tra chức năng in | Giao diện in ẩn thanh điều hướng; chỉ hiển thị bảng dữ liệu |

*Bảng 3.11. Kiểm thử giao diện Báo cáo Tồn kho*

---

## 3.12. Giao diện Đăng nhập

Giao diện đăng nhập gồm form với hai trường bắt buộc: **Email** (có icon phong bì) và **Mật khẩu** (có icon khóa), checkbox **"Ghi nhớ đăng nhập"**, nút **"Đăng nhập"** và link **"Tôi quên mật khẩu"**.

Khi nhập sai email hoặc mật khẩu, hệ thống hiển thị thông báo lỗi đỏ ngay dưới trường nhập. Khi nhập đúng thông tin, hệ thống tạo phiên đăng nhập và chuyển hướng đến trang dashboard theo đúng vai trò của người dùng. Hệ thống không có chức năng đăng ký công khai — tài khoản được Admin tổng tạo thủ công.

*Hình 3.12. Giao diện Đăng nhập*

### 3.12.1. Kiểm thử chức năng Đăng nhập

| Chức năng | Lập kế hoạch kiểm thử | Thực thi |
|-----------|----------------------|----------|
| Đăng nhập hợp lệ | Nhập đúng email và mật khẩu của tài khoản đang hoạt động | Đăng nhập thành công, chuyển hướng đến dashboard |
| Đăng nhập sai mật khẩu | Nhập email đúng nhưng mật khẩu sai | Thông báo lỗi "Thông tin đăng nhập không khớp"; không vào được hệ thống |
| Đăng nhập sai email | Nhập email không tồn tại trong hệ thống | Thông báo lỗi; không vào được hệ thống |
| Để trống trường | Gửi form khi để trống email hoặc mật khẩu | Trình duyệt yêu cầu nhập đầy đủ (HTML5 required validation) |
| Tài khoản bị khóa | Đăng nhập bằng tài khoản có trạng thái "Đã khóa" | Thông báo lỗi; không vào được hệ thống |
| Ghi nhớ đăng nhập | Tick checkbox "Ghi nhớ đăng nhập" rồi đăng nhập | Phiên đăng nhập được duy trì sau khi đóng trình duyệt |
| Quên mật khẩu | Nhấn link "Tôi quên mật khẩu" | Chuyển đến trang yêu cầu đặt lại mật khẩu qua email |

*Bảng 3.12. Kiểm thử giao diện Đăng nhập*
