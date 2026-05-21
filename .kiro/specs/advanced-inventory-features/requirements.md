# Requirements Document

## Introduction

Tài liệu yêu cầu cho bộ tính năng nâng cao của hệ thống Quản lý Vật tư (Inventory Management System). Hệ thống hiện tại là ứng dụng Laravel với AdminLTE v4, hỗ trợ 3 vai trò (Admin tổng, Admin kho, Nhân viên kho) và các nghiệp vụ nhập/xuất/chuyển/kiểm kê kho. Bộ tính năng mới bao gồm: Lọc/Tìm kiếm nâng cao, Thẻ kho, Activity Log, Thông báo, Purchase Order, Batch Tracking, Phiếu trả hàng, Báo cáo nâng cao, và Phân loại vật tư.

## Glossary

- **System**: Hệ thống Quản lý Vật tư (toàn bộ ứng dụng Laravel)
- **Filter_Engine**: Module xử lý lọc và tìm kiếm trên các trang danh sách
- **Stock_Card_Module**: Module hiển thị thẻ kho (lịch sử giao dịch nhập/xuất/chuyển) cho từng vật tư tại từng kho
- **Activity_Log_Service**: Service ghi nhận nhật ký hoạt động (audit trail) cho các thao tác trên phiếu
- **Notification_Service**: Service gửi thông báo realtime đến người dùng
- **Purchase_Order_Module**: Module quản lý đơn đặt hàng (tạo, duyệt, liên kết phiếu nhập)
- **Batch_Tracking_Module**: Module theo dõi lô hàng (lot number, ngày sản xuất, hạn sử dụng, FIFO)
- **Return_Voucher_Module**: Module quản lý phiếu trả hàng (trả nhà cung cấp, trả từ công trình)
- **Report_Engine**: Module tạo báo cáo nâng cao (nhập/xuất theo kỳ, dự toán vs thực tế, biến động chi phí)
- **Category_Module**: Module phân loại vật tư theo nhóm (xây dựng, điện, nước, v.v.)
- **Admin_Tong**: Vai trò Admin tổng - quản lý toàn bộ hệ thống, tất cả kho
- **Admin_Kho**: Vai trò Admin kho - quản lý một kho cụ thể
- **Nhan_Vien_Kho**: Vai trò Nhân viên kho - thao tác nghiệp vụ tại một kho
- **Voucher**: Phiếu nghiệp vụ (phiếu nhập, xuất, chuyển, kiểm kê, trả hàng)
- **FIFO**: First In First Out - phương pháp xuất kho theo lô nhập trước

## Requirements

### Requirement 1: Advanced Filtering on List Pages

**User Story:** As a warehouse user, I want to filter and search voucher lists by multiple criteria, so that I can quickly find specific transactions without scrolling through all records.

#### Acceptance Criteria

1. WHEN a user accesses a voucher list page (entries, exits, transfers, checks), THE Filter_Engine SHALL display filter controls for date range, status, warehouse, supplier, project, and user
2. WHEN a user applies one or more filter criteria, THE Filter_Engine SHALL return only records matching all selected criteria
3. WHEN a user enters text in the search field, THE Filter_Engine SHALL filter materials by name using partial matching (LIKE search)
4. WHEN a user clears all filters, THE Filter_Engine SHALL display the complete unfiltered list
5. WHILE an Admin_Kho or Nhan_Vien_Kho is viewing a list page, THE Filter_Engine SHALL restrict the warehouse filter to the user's assigned warehouse only
6. WHEN a user applies filters, THE Filter_Engine SHALL preserve the selected filter values after page reload or pagination navigation
7. WHEN a user accesses the materials list page, THE Filter_Engine SHALL provide filter controls for material name, unit, and stock status (below minimum, normal, above maximum)

### Requirement 2: Stock Card (Thẻ kho)

**User Story:** As a warehouse manager, I want to view the complete transaction history for each material at each warehouse, so that I can trace all stock movements over time.

#### Acceptance Criteria

1. WHEN a user selects a material and warehouse combination, THE Stock_Card_Module SHALL display a chronological list of all transactions (entries, exits, transfers in, transfers out) for that material at that warehouse
2. THE Stock_Card_Module SHALL display for each transaction: date, voucher type, voucher number, quantity in, quantity out, running balance, and related party (supplier/project/warehouse)
3. WHEN a user specifies a date range on the stock card, THE Stock_Card_Module SHALL filter transactions to only those within the specified period
4. THE Stock_Card_Module SHALL calculate and display the opening balance at the start of the selected period
5. THE Stock_Card_Module SHALL calculate and display the closing balance at the end of the selected period
6. WHEN a user exports the stock card, THE Stock_Card_Module SHALL generate an Excel or PDF file containing all displayed transactions with opening and closing balances

### Requirement 3: Activity Log (Audit Trail)

**User Story:** As an administrator, I want to see who approved, cancelled, or modified vouchers and when, so that I have a complete audit trail for accountability.

#### Acceptance Criteria

1. WHEN a user approves a voucher, THE Activity_Log_Service SHALL record the user identity, voucher type, voucher ID, action (approved), and timestamp
2. WHEN a user cancels a voucher, THE Activity_Log_Service SHALL record the user identity, voucher type, voucher ID, action (cancelled), and timestamp
3. WHEN a user creates a voucher, THE Activity_Log_Service SHALL record the user identity, voucher type, voucher ID, action (created), and timestamp
4. WHEN a user views the activity log for a voucher, THE Activity_Log_Service SHALL display all recorded actions in reverse chronological order
5. WHEN an Admin_Tong accesses the global activity log page, THE Activity_Log_Service SHALL display all system-wide activities with filtering by user, action type, voucher type, and date range
6. THE Activity_Log_Service SHALL store the previous and new values for status changes on vouchers
7. IF the Activity_Log_Service fails to write a log entry, THEN THE System SHALL still complete the original operation and queue the log entry for retry

### Requirement 4: Notifications

**User Story:** As a warehouse manager, I want to receive notifications when vouchers need my approval or when stock alerts are triggered, so that I can respond promptly without constantly checking the system.

#### Acceptance Criteria

1. WHEN a new voucher is created with status "pending", THE Notification_Service SHALL send a notification to all Admin_Kho users of the relevant warehouse and all Admin_Tong users
2. WHEN an inventory alert is triggered (stock below minimum level), THE Notification_Service SHALL send a realtime notification to Admin_Kho of the affected warehouse and all Admin_Tong users
3. THE Notification_Service SHALL display a notification bell icon with unread count in the application header
4. WHEN a user clicks the notification bell, THE Notification_Service SHALL display a dropdown list of recent notifications sorted by newest first
5. WHEN a user clicks a notification, THE Notification_Service SHALL mark the notification as read and navigate to the relevant voucher or alert page
6. WHEN a user clicks "Mark all as read", THE Notification_Service SHALL mark all unread notifications as read for that user
7. THE Notification_Service SHALL update the notification count in realtime without requiring page refresh

### Requirement 5: Purchase Order Management

**User Story:** As a warehouse manager, I want to create purchase orders, get them approved, and generate inventory entries from approved POs, so that I can track procurement from request to receipt.

#### Acceptance Criteria

1. WHEN a user creates a purchase order, THE Purchase_Order_Module SHALL require: date, supplier, at least one material with quantity and unit price, and optional expected delivery date and note
2. THE Purchase_Order_Module SHALL calculate the total amount as the sum of (quantity × unit price) for all line items
3. WHEN an Admin_Tong or Admin_Kho approves a purchase order, THE Purchase_Order_Module SHALL change the status from "pending" to "approved"
4. WHEN an Admin_Tong or Admin_Kho cancels a purchase order, THE Purchase_Order_Module SHALL change the status from "pending" to "cancelled"
5. WHEN a user generates an inventory entry from an approved purchase order, THE Purchase_Order_Module SHALL create a new inventory entry pre-filled with the PO's supplier, materials, quantities, and unit prices
6. WHEN an inventory entry is generated from a purchase order, THE Purchase_Order_Module SHALL store the link between the purchase order and the inventory entry (purchase_order_id on the entry)
7. WHEN the linked inventory entry is approved, THE Purchase_Order_Module SHALL change the purchase order status from "approved" to "completed"
8. WHEN a user views a purchase order, THE Purchase_Order_Module SHALL display the linked inventory entry (if any) with its current status
9. IF a user attempts to generate an inventory entry from a purchase order that already has a linked entry, THEN THE Purchase_Order_Module SHALL reject the action and display an error message

### Requirement 6: Batch/Lot Tracking

**User Story:** As a warehouse manager, I want to track materials by batch/lot with expiry dates and manufacturing dates, so that I can manage perishable materials and ensure FIFO compliance.

#### Acceptance Criteria

1. WHEN a user creates an inventory entry, THE Batch_Tracking_Module SHALL allow specifying batch code, manufacturing date, and expiry date for each material line item
2. THE Batch_Tracking_Module SHALL store batch stock separately per material, warehouse, and batch code combination
3. WHEN a user creates an inventory exit, THE Batch_Tracking_Module SHALL automatically select batches using FIFO order (oldest manufacturing date first)
4. WHEN a user creates an inventory exit, THE Batch_Tracking_Module SHALL allow manual batch selection as an override to FIFO
5. IF the requested exit quantity exceeds the available stock in the oldest batch, THEN THE Batch_Tracking_Module SHALL split the exit across multiple batches following FIFO order
6. WHEN a batch stock reaches zero, THE Batch_Tracking_Module SHALL mark the batch as depleted but retain the record for historical reference
7. WHEN a user views material stock details, THE Batch_Tracking_Module SHALL display all active batches with their batch code, manufacturing date, expiry date, and remaining stock
8. THE Batch_Tracking_Module SHALL highlight batches that are within 30 days of expiry with a visual warning indicator

### Requirement 7: Return Vouchers (Phiếu trả hàng)

**User Story:** As a warehouse user, I want to create return vouchers for returning materials to suppliers or receiving returns from projects, so that stock adjustments from returns are properly tracked.

#### Acceptance Criteria

1. WHEN a user creates a supplier return voucher, THE Return_Voucher_Module SHALL require: date, warehouse, supplier, at least one material with quantity, and a return reason (defective, wrong item, excess)
2. WHEN a supplier return voucher is approved, THE Return_Voucher_Module SHALL subtract the returned quantities from the warehouse stock
3. WHEN a supplier return voucher is approved, THE Return_Voucher_Module SHALL recalculate the weighted average cost for affected materials
4. WHEN a user creates a project return voucher, THE Return_Voucher_Module SHALL require: date, warehouse, project, at least one material with quantity, and a return reason (excess, project completed, unused)
5. WHEN a project return voucher is approved, THE Return_Voucher_Module SHALL add the returned quantities back to the warehouse stock
6. WHEN a project return voucher is approved, THE Return_Voucher_Module SHALL update the actual used quantity for the project's BoQ tracking
7. THE Return_Voucher_Module SHALL follow the same approval workflow as other vouchers (pending → approved/cancelled)
8. WHEN a user views a return voucher, THE Return_Voucher_Module SHALL display the return reason and link to the original entry or exit voucher (if specified)

### Requirement 8: Advanced Reports

**User Story:** As an administrator, I want to generate detailed reports on inventory movements, cost variances, and project consumption, so that I can make informed decisions about procurement and resource allocation.

#### Acceptance Criteria

1. WHEN a user selects a time period, THE Report_Engine SHALL generate an entry/exit summary report showing total quantities and values grouped by material
2. WHEN a user selects a project, THE Report_Engine SHALL generate a comparison report showing estimated quantities (from BoQ) versus actual consumed quantities for each material
3. THE Report_Engine SHALL calculate cost variance as the difference between planned unit cost and actual weighted average cost for each material in a project
4. WHEN a user requests a top materials report, THE Report_Engine SHALL display the top 10 materials by total exit quantity or total exit value within a specified period
5. WHEN a user requests a trend report, THE Report_Engine SHALL display monthly entry and exit quantities for a selected material over the past 12 months as a line chart
6. WHEN a user selects a warehouse and time period, THE Report_Engine SHALL generate a stock movement report showing opening stock, total entries, total exits, adjustments, and closing stock per material
7. THE Report_Engine SHALL allow exporting all reports to Excel and PDF formats
8. WHILE an Admin_Kho is viewing reports, THE Report_Engine SHALL restrict data to the user's assigned warehouse only

### Requirement 9: Material Categories (Phân loại vật tư)

**User Story:** As an administrator, I want to organize materials into categories (construction, electrical, plumbing, etc.), so that I can filter and report on materials by group.

#### Acceptance Criteria

1. THE Category_Module SHALL provide a categories management page where Admin_Tong and Admin_Kho can create, edit, and delete material categories
2. THE Category_Module SHALL require each category to have a unique name and optional description
3. WHEN a user creates or edits a material, THE Category_Module SHALL allow assigning the material to one category
4. WHEN a user views the materials list, THE Category_Module SHALL display the category name for each material and allow filtering by category
5. WHEN a user generates reports, THE Report_Engine SHALL allow filtering report data by one or more material categories
6. IF a user attempts to delete a category that has materials assigned, THEN THE Category_Module SHALL prevent deletion and display the count of assigned materials
7. THE Category_Module SHALL support hierarchical categories with one level of nesting (parent category and sub-categories)
