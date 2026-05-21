# CHƯƠNG 1. GIỚI THIỆU BÀI TOÁN QUẢN LÝ VẬT TƯ

## 1.1. Mô tả tổng quan bài toán
Việc quản lý số lượng lớn vật tư, kho bãi và các hoạt động vận hành trong các doanh nghiệp – đặc biệt tại các công ty sản xuất, xây dựng hay kinh doanh thương mại – đòi hỏi sự chuẩn bị kỹ lưỡng, quy trình chặt chẽ và tiêu tốn nhiều nguồn lực. Các phương pháp quản lý truyền thống thường yêu cầu ghi chép thủ công thông tin vật tư, theo dõi số lượng nhập xuất, quản lý nhà cung cấp và xử lý những vấn đề phát sinh trong quá trình lưu kho, điều này dễ dẫn đến sai sót trong kiểm kê và tốn nhiều thời gian. Bên cạnh đó, việc lưu trữ và quản lý dữ liệu về lịch sử giao dịch, giá trị tồn kho hay hao hụt cũng gặp nhiều khó khăn, dễ dẫn đến thất thoát thông tin hoặc nhầm lẫn giữa các lô hàng, ảnh hưởng đến tính minh bạch và hiệu quả sử dụng vốn của doanh nghiệp.

Hơn nữa, khi quy mô và số lượng chủng loại vật tư tăng cao, thủ kho phải dành thêm nhiều công sức cho việc kiểm tra tình trạng hàng hóa, quản lý hạn mức tồn kho, đôn đốc nhập hàng kịp thời và xử lý các yêu cầu cấp phát vật tư từ các dự án/phòng ban, gây áp lực lớn lên hệ thống quản lý truyền thống. Việc thiếu các công cụ hỗ trợ quản lý tập trung có thể khiến quy trình lập phiếu xuất/nhập, theo dõi công nợ nhà cung cấp, quản lý lô hàng và giải quyết các vấn đề thiếu hụt vật tư trở nên phức tạp, làm giảm hiệu quả hoạt động kinh doanh.

Những hạn chế này đặt ra yêu cầu cấp thiết đối với một giải pháp công nghệ hiện đại, giúp tự động hóa quy trình quản lý vật tư, giảm thiểu sai sót trong kiểm kê, tiết kiệm thời gian và nguồn lực, đồng thời nâng cao tính minh bạch, độ chính xác và hiệu quả trong hoạt động vận hành kho bãi.

## 1.2. Tìm hiểu bài toán quản lý vật tư

### 1.2.1. Mô tả thông tin danh mục vật tư
Quá trình quản lý thông tin các danh mục vật tư trong kho (ví dụ: nguyên vật liệu, công cụ dụng cụ, thiết bị, linh kiện...) thường được lưu trữ dưới dạng hồ sơ giấy hoặc các công cụ bảng tính như Excel. Để quản lý đầy đủ danh mục này, thủ kho sẽ thu thập dữ liệu cụ thể cho từng loại vật tư. Quy trình chi tiết như sau:
- **Lưu trữ thông tin danh mục vật tư:** Khi mở rộng kinh doanh hoặc nhập thêm hàng mới, doanh nghiệp sẽ thiết lập các nhóm danh mục vật tư khác nhau để phân loại. Những thông tin cơ bản bao gồm: mã loại, tên loại vật tư, và mô tả chi tiết. Thủ kho thường tự ghi chép hoặc nhập dữ liệu vào Excel để phân loại và làm căn cứ quản lý.
- **Kiểm tra và cập nhật thông tin:** Dữ liệu về danh mục được kiểm tra thường xuyên dựa trên tình hình nhập hàng thực tế. Nếu có sự thay đổi như ngừng kinh doanh một loại hàng hoặc thêm nhóm hàng mới, thủ kho sẽ cập nhật lại dữ liệu để đảm bảo tính chính xác khi tra cứu.

### 1.2.2. Mô tả thông tin chi tiết vật tư
Quản lý thông tin chi tiết từng vật tư là nhiệm vụ cốt lõi, đòi hỏi sự chính xác để đảm bảo vận hành kho trơn tru. Thông tin thường được lưu trữ thủ công hoặc bằng Excel, giúp thủ kho theo dõi tình trạng hàng hóa. Quy trình bao gồm:
- **Lưu trữ thông tin vật tư:** Mỗi vật tư sẽ được định danh rõ ràng. Các thông tin bao gồm: mã vật tư, tên vật tư, thuộc danh mục nào, số lượng tồn kho hiện tại, đơn vị tính, hình ảnh và giá tham khảo. Những dữ liệu này giúp doanh nghiệp biết chính xác trong kho có những mặt hàng nào và giá trị là bao nhiêu.
- **Kiểm tra và cập nhật thông tin:** Thông tin vật tư được cập nhật liên tục qua các kỳ kiểm kê hoặc khi có biến động giá. Nếu phát hiện hao hụt hoặc thay đổi thông số kỹ thuật, thủ kho sẽ ghi chép lại ngay để kịp thời điều chỉnh thông tin, tránh nhầm lẫn khi cấp phát.

### 1.2.3. Mô tả thông tin lô hàng và hạn mức tồn kho
Quản lý số lượng và tình trạng tồn kho là nghiệp vụ quan trọng trong vận hành kho bãi. Đây là quy trình theo dõi sát sao sự biến động hàng hóa.
- **Theo dõi hạn mức tồn kho:** Hàng tháng/quý, thủ kho sẽ kiểm tra và đối chiếu số lượng thực tế với số liệu trên sổ sách. Việc nắm bắt số liệu giúp thiết lập các mức cảnh báo tồn kho tối thiểu/tối đa để có kế hoạch nhập bổ sung hàng kịp thời.
- **Quản lý lô hàng (Batch):** Khi vật tư được nhập về kho, chúng thường đi theo lô. Quản lý thông tin từng lô giúp áp dụng nguyên tắc quản lý kho hiệu quả và theo dõi sát sao chất lượng vật tư theo thời gian.

### 1.2.4. Mô tả thông tin phiếu nhập và phiếu xuất
Quản lý quá trình nhập/xuất kho đóng vai trò then chốt trong việc đảm bảo tính minh bạch luồng hàng hóa.
- **Lập phiếu nhập kho:** Khi có hàng từ nhà cung cấp chuyển đến, bộ phận kho lập phiếu nhập bao gồm: ngày nhập, mã nhà cung cấp, thông tin các vật tư, số lượng, đơn giá và tổng tiền.
- **Lập phiếu xuất kho:** Khi có yêu cầu cấp vật tư từ phòng ban/dự án, kho tiến hành xuất hàng và lập phiếu xuất bao gồm: ngày xuất, mã dự án/người nhận, danh sách vật tư và số lượng thực xuất.
- **Cập nhật số lượng tồn kho:** Sau mỗi lần lập phiếu nhập hoặc xuất, số lượng tồn kho của các vật tư tương ứng phải được cộng hoặc trừ vào hệ thống một cách chuẩn xác nhất.

### 1.2.5. Mô tả thông tin nhà cung cấp và dự án/phòng ban
Quản lý các đối tượng liên quan đến luồng nhập và luồng xuất của vật tư.
- **Thu thập thông tin nhà cung cấp:** Ghi nhận tên đơn vị, địa chỉ, số điện thoại, email, và người liên hệ. Thông tin này giúp doanh nghiệp đánh giá đối tác và thuận tiện trong việc đặt hàng.
- **Theo dõi dự án/phòng ban:** Ghi nhận các phòng ban nội bộ hoặc các dự án công trình đang thi công để làm căn cứ cấp phát vật tư. Việc này giúp hạch toán chi phí chính xác cho từng đối tượng sử dụng.

### 1.2.6. Mô tả thông tin nhân viên (Thủ kho/Người quản lý)
Đối với các kho vật tư lớn, doanh nghiệp thường bố trí nhân viên quản lý kho, thủ kho, hoặc nhân viên vận hành.
- **Lưu trữ thông tin nhân viên:** Ghi nhận họ tên, chức vụ, thông tin liên lạc. Việc quản lý quyền hạn của nhân viên giúp đảm bảo an ninh kho bãi.
- **Phân quyền công việc:** Phân quyền rõ ràng trên phần mềm (ai được lập phiếu xuất, ai được sửa thông tin vật tư, ai được phê duyệt) để kiểm soát chặt chẽ trách nhiệm từng cá nhân.

### 1.2.7. Mô tả thông tin thống kê báo cáo
Thống kê báo cáo giúp ban giám đốc đánh giá tình hình sử dụng vốn và đưa ra các quyết định nhập xuất hàng hóa kịp thời.
- **Báo cáo tồn kho:** Tổng hợp số lượng và giá trị tồn kho của từng vật tư tại một thời điểm nhất định.
- **Báo cáo nhập xuất:** Thống kê chi tiết lượng hàng nhập vào và xuất ra trong một khoảng thời gian (tuần/tháng/quý).
- **Báo cáo cảnh báo:** Cảnh báo các vật tư đã dưới mức tồn kho tối thiểu cần nhập gấp, hoặc vật tư tồn đọng lâu ngày cần xử lý.

## 1.3. Một số ứng dụng/phần mềm liên quan đến đề tài

### 1.3.1. Hệ thống Quản lý kho vật tư hiện đại (Ví dụ: KiotViet, Sapo, ERPNext)
Các chức năng chính của hệ thống quản lý kho vật tư tiêu chuẩn:
- **Quản lý thông tin vật tư và danh mục:** Chức năng này cho phép doanh nghiệp lưu trữ và theo dõi toàn bộ dữ liệu liên quan đến hàng hóa. Hệ thống hỗ trợ thêm mới, chỉnh sửa hoặc xóa thông tin vật tư khi có sự thay đổi. Các thông tin quan trọng như mã vật tư, tên gọi, hình ảnh, đơn vị tính, danh mục đều được lưu trữ khoa học. Ngoài ra, hệ thống còn cung cấp tính năng tìm kiếm và lọc vật tư, xem tình trạng tồn kho, giúp thủ kho nắm bắt tình hình một cách nhanh chóng.
- **Quản lý nghiệp vụ Nhập - Xuất kho:** Đây là chức năng cốt lõi hỗ trợ tạo và theo dõi các luồng giao dịch hàng hóa. Hệ thống cho phép lập phiếu nhập kho từ nhà cung cấp, phiếu xuất kho cho các dự án/phòng ban. Mỗi khi phiếu được xác nhận, hệ thống tự động cộng/trừ số lượng trên tồn kho hiện tại. Điều này giúp đảm bảo tính chính xác tuyệt đối, tránh sai sót so với tính toán thủ công. Hệ thống cũng cung cấp tính năng lưu trữ lịch sử để đối chiếu số liệu bất cứ khi nào.
- **Quản lý nhà cung cấp, dự án và báo cáo:** Chức năng này giúp doanh nghiệp theo dõi toàn bộ mạng lưới đối tác và nơi tiêu thụ vật tư. Việc lưu trữ thông tin nhà cung cấp và lịch sử nhập hàng giúp đánh giá chất lượng đối tác. Hệ thống cũng tự động tạo các biểu đồ, báo cáo tồn kho, báo cáo nhập xuất theo thời gian thực. Báo cáo này giúp ban giám đốc có cái nhìn tổng quan để tối ưu hóa nguồn vốn lưu động.

## 1.4. Các yêu cầu mới cho hệ thống
Hệ thống quản lý vật tư khi xây dựng phải là một hệ thống quản trị nội bộ tập trung, phân quyền rõ ràng, đáp ứng được các yêu cầu thực tế sau:
- Phải có hệ thống quản trị mạnh mẽ để quản lý toàn bộ danh mục, vật tư, phiếu nhập, phiếu xuất, nhà cung cấp, dự án và thống kê tồn kho.
- Áp dụng cơ chế phân quyền người dùng chặt chẽ (Admin tổng, Admin kho, và các tài khoản nhân viên được cấp quyền truy cập giới hạn) để đảm bảo việc vận hành kho một cách an toàn và bảo mật, giới hạn đúng chức năng cho đúng người.

### 1.4.1. Yêu cầu chức năng

**I. QUYỀN CỦA ADMIN TỔNG (QUẢN TRỊ VIÊN CẤP CAO)**

*(Lưu ý: Admin tổng được thừa hưởng toàn bộ quyền của Admin kho và Nhân viên kho, đồng thời là role duy nhất có các quyền hạn hệ thống sau)*

| STT | Nội dung yêu cầu | Mô tả |
|---|---|---|
| 1.1 | Quản lý người dùng (Users) | Thêm mới, sửa, xóa tài khoản của tất cả nhân viên trong hệ thống. |
| 1.2 | Phân quyền hệ thống (Roles) | Thiết lập và phân quyền truy cập cho các tài khoản. |
| 1.3 | Quản lý chi nhánh Kho | Quyền duy nhất được thêm mới, cập nhật hoặc xóa các điểm kho (Warehouse). |

**II. QUYỀN CỦA ADMIN KHO (QUẢN LÝ TẠI KHO CHI NHÁNH)**

*(Lưu ý: Admin kho thừa hưởng các quyền lập phiếu, xem báo cáo của Nhân viên kho và có thêm các quyền quản lý nghiệp vụ sau)*

| STT | Nội dung yêu cầu | Mô tả |
|---|---|---|
| 2.1 | Quản lý vật tư & danh mục | Thêm mới, sửa, xóa và Import/Export Excel danh mục vật tư, đơn vị tính. |
| 2.2 | Quản lý nhà cung cấp, dự án | Thêm mới, sửa, xóa danh sách nhà cung cấp vật tư và các dự án/phòng ban. |
| 2.3 | Phê duyệt/Hủy phiếu kho | Có quyền duyệt hoặc hủy (Approve/Cancel) các phiếu nhập, xuất, chuyển kho, kiểm kê do nhân viên lập. |
| 2.4 | Xử lý cảnh báo tồn kho | Nhận cảnh báo vật tư dưới hạn mức tối thiểu và đánh dấu trạng thái đã xử lý (Resolve). |

**III. QUYỀN CỦA NHÂN VIÊN KHO (NGƯỜI DÙNG CƠ BẢN)**

| STT | Nội dung yêu cầu | Mô tả |
|---|---|---|
| 3.1 | Tra cứu thông tin | Được phép xem danh sách vật tư, danh mục, nhà cung cấp, dự án và danh sách kho. |
| 3.2 | Lập phiếu giao dịch kho | Lập phiếu nhập kho, phiếu xuất kho, phiếu chuyển kho giữa các chi nhánh (ở trạng thái chờ duyệt). |
| 3.3 | Kiểm kê kho | Lập phiếu kiểm kê đối chiếu số lượng thực tế với hệ thống (chờ duyệt). |
| 3.4 | Xuất báo cáo, thống kê | Xem báo cáo tồn kho, xuất ra file Excel/PDF chi tiết các phiếu nhập, xuất, báo cáo kiểm kê. |

### 1.4.2. Yêu cầu phi chức năng
Những yêu cầu phi chức năng giúp đảm bảo website quản lý vật tư không chỉ bảo mật dữ liệu doanh nghiệp mà còn đáp ứng các tiêu chuẩn về vận hành ổn định, an toàn và thuận tiện:
- **Bảo mật:** Đảm bảo toàn bộ thông tin về lượng tồn kho, giá trị tài sản, thông tin nhà cung cấp và dữ liệu tài chính nội bộ được bảo mật tuyệt đối. Hệ thống cài đặt cơ chế đăng nhập và phân quyền chặt chẽ để bảo vệ dữ liệu khỏi truy cập trái phép.
- **Hiệu suất:** Website phải hoạt động ổn định và xử lý nhanh chóng ngay cả khi hệ thống có lượng dữ liệu lớn (hàng ngàn mã vật tư, hàng vạn giao dịch nhập xuất). Các thao tác nghiệp vụ như tính toán tồn kho hay tải báo cáo phải được thực hiện mượt mà.
- **Dễ dàng bảo trì và mở rộng:** Hệ thống được thiết kế theo kiến trúc MVC, giúp việc bảo trì và nâng cấp trở nên thuận tiện. Có khả năng mở rộng thêm các tính năng trong tương lai như: quét mã vạch (Barcode), xuất hóa đơn điện tử, tích hợp phần mềm kế toán.
- **Thiết kế đáp ứng (Responsive Design):** Giao diện website hiển thị tối ưu trên mọi loại thiết bị từ máy tính để bàn (dành cho văn phòng) đến điện thoại di động (dành cho thủ kho kiểm tra nhanh tại kho).
- **Nội dung dữ liệu đồng bộ từ cơ sở dữ liệu:** Mọi thông tin hiển thị trên website như số lượng tồn kho, lịch sử phiếu nhập xuất đều được truy xuất trực tiếp từ cơ sở dữ liệu. Điều này đảm bảo tính thống nhất, chính xác và dễ dàng trong việc quản lý tập trung.
- **Quy chuẩn về giao diện và hiển thị:** Sử dụng các bộ font chữ hiện đại, rõ ràng kết hợp với bố cục khoa học nhằm tăng trải nghiệm người dùng khi phải xem các bảng biểu dữ liệu lớn.
- **Công nghệ và công cụ sử dụng:** Hệ thống được xây dựng trên nền tảng công nghệ mạnh mẽ bao gồm HTML, CSS, JavaScript, Bootstrap/Tailwind CSS và framework PHP Laravel kết hợp với hệ quản trị cơ sở dữ liệu MySQL. Đảm bảo tính hiện đại, mở rộng và bảo mật tốt.
