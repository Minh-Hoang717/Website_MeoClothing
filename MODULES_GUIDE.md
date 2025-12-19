# 📊 Module Báo Cáo & Marketing - Hướng Dẫn

## 1. Module Marketing - Quản Lý Mã Khuyến Mãi

### 📍 Vị Trí

- **Controller**: [app/controllers/PromotionController.php](../app/controllers/PromotionController.php)
- **Views**:
  - [app/views/admin/promotions/index.php](../app/views/admin/promotions/index.php) - Danh sách
  - [app/views/admin/promotions/add.php](../app/views/admin/promotions/add.php) - Thêm mới
  - [app/views/admin/promotions/edit.php](../app/views/admin/promotions/edit.php) - Chỉnh sửa

### 🎯 Chức Năng CRUD

#### **1. Danh sách mã khuyến mãi**

```
URL: /admin/promotions
Method: GET
Chức năng: Hiển thị danh sách tất cả mã khuyến mãi với trạng thái (Hoạt Động/Chưa Bắt Đầu/Hết Hạn)
```

#### **2. Thêm mã khuyến mãi mới**

```
URL: /admin/promotions/add
Method: GET (form) / POST (submit)
Tham số:
  - code: Mã khuyến mãi (VD: SUMMER20)
  - discount_type: 'fixed' hoặc 'percentage'
  - discount_value: Giá trị giảm (% hoặc VNĐ)
  - start_date: Ngày bắt đầu
  - end_date: Ngày kết thúc
```

#### **3. Chỉnh sửa mã khuyến mãi**

```
URL: /admin/promotions/edit/{id}
Method: GET (form) / POST (submit)
Tham số: Tương tự thêm mới
```

#### **4. Xóa mã khuyến mãi**

```
URL: /admin/promotions/delete-ajax
Method: POST (AJAX)
Payload: {"id": 1}
Response: {"success": true, "message": "Xoá mã khuyến mãi thành công"}
```

### 🔍 Ví Dụ Tạo Mã Khuyến Mãi

**Loại 1: Giảm Phần Trăm**

```
Mã: SUMMER30
Loại: Phần Trăm (%)
Giá trị: 30
=> Khách hàng sẽ được giảm 30% trên tổng đơn hàng
```

**Loại 2: Giảm Tiền Cố Định**

```
Mã: SAVE50K
Loại: Tiền Cố Định (đ)
Giá trị: 50000
=> Khách hàng sẽ được giảm 50.000đ trên tổng đơn hàng
```

### ✅ Validation

- **Mã khuyến mãi**:

  - Tối thiểu 2 ký tự
  - Không được trùng lặp
  - Tự động chuyển thành CHỮ HOA

- **Giá trị giảm**:

  - Phải lớn hơn 0
  - Nếu là %, không vượt quá 100%
  - Nếu là tiền, tính bằng VNĐ

- **Ngày kết thúc**:
  - Phải sau ngày bắt đầu
  - Hỗ trợ datetime-local input

### 🟢 Trạng Thái Mã

| Trạng Thái      | Điều Kiện                             | Biểu Tượng |
| --------------- | ------------------------------------- | ---------- |
| ✅ Hoạt Động    | now >= start_date AND now <= end_date | Green      |
| ⏳ Chưa Bắt Đầu | now < start_date                      | Yellow     |
| ❌ Hết Hạn      | now > end_date                        | Red        |

---

## 2. Module Báo Cáo - Dashboard Analytics

### 📍 Vị Trí

- **Controller**: [app/controllers/AdminController.php](../app/controllers/AdminController.php) - method `index()`
- **Model**: [app/models/Order.php](../app/models/Order.php) - methods `getTotalRevenue()`, `getTopProducts()`
- **View**: [app/views/admin/index.php](../app/views/admin/index.php)

### 📊 Chỉ Số Báo Cáo

#### **1. Tổng Doanh Thu** 💰

```php
Method: $orderModel->getTotalRevenue()
Query: SELECT SUM(total_amount) FROM orders WHERE status = 'completed'
Hiển thị: Dashboard (stat card)
Tính toán: Tổng tiền từ tất cả đơn hàng đã hoàn thành
```

**Công thức:**

```
Tổng Doanh Thu = Σ(total_amount) của tất cả đơn hàng có status = 'completed'
```

#### **2. Top 5 Sản Phẩm Bán Chạy** 🔥

```php
Method: $orderModel->getTopProducts(5)
Query: SELECT SUM(od.quantity), COUNT(DISTINCT od.order_id), AVG(od.unit_price)
       FROM orderdetails od
       JOIN productvariants pv ...
       JOIN products p ...
       GROUP BY product_id
       ORDER BY SUM(od.quantity) DESC
       LIMIT 5

Hiển thị: Bảng với xếp hạng (Top 1, 2, 3, 4, 5)
Cột dữ liệu:
  - Xếp hạng (#)
  - Tên sản phẩm
  - Số lượng bán
  - Số đơn hàng
  - Giá trung bình
```

**Công thức tính:**

```
Số Lượng Bán = SUM(quantity) từ tất cả orderdetails của sản phẩm
Số Đơn Hàng = COUNT(DISTINCT order_id) chứa sản phẩm
Giá Trung Bình = AVG(unit_price) của sản phẩm
```

### 📈 Dashboard Layout

```
┌─────────────────────────────────────────────────────┐
│               📊 DASHBOARD                          │
├─────────────────────────────────────────────────────┤
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│  │🛍️ Sản Phẩm│  │📦 Đơn Hàng│  │👥 Người Dùng│  │💰 Doanh Thu│
│  │    50   │  │   150   │  │   200   │  │ 500.0M  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘
│
│  ┌─────────────────────────────────────────────────┐
│  │  🔥 TOP 5 BEST-SELLING PRODUCTS                 │
│  ├──┬──────────────────┬─────┬─────┬──────────────┤
│  │#│ Tên Sản Phẩm      │ Số Bán│ Đơn │ Giá Trung B │
│  ├──┼──────────────────┼─────┼─────┼──────────────┤
│  │1│ Áo Thun Nam Tây Âu│ 250 │  80 │   150,000đ  │
│  │2│ Quần Jean Xanh    │ 180 │  60 │   299,000đ  │
│  │3│ Áo Sơ Mi Trắng    │ 145 │  45 │   199,000đ  │
│  │4│ Mũ Lưỡi Trai      │ 120 │  40 │    89,000đ  │
│  │5│ Túi Xách Đen      │ 100 │  30 │   399,000đ  │
│  └──┴──────────────────┴─────┴─────┴──────────────┘
│
│  🔗 QUICK LINKS: [Sản Phẩm] [Thêm SP] [Mã Khuyến Mãi] [Đơn Hàng]
└─────────────────────────────────────────────────────┘
```

### 🎨 Visual Design

- **Stat Cards**:

  - Gradient backgrounds (Blue, Green, Purple, Orange)
  - Hover effect (translate up -5px)
  - Hiển thị emoji + tên + giá trị

- **Rank Badges**:

  - Top 1 (Gold): Vàng
  - Top 2 (Silver): Bạc
  - Top 3 (Bronze): Đồng
  - Top 4-5: Purple/Violet

- **Report Table**:
  - Gradient header (Purple)
  - Row hover effect
  - Quantity badges (Blue)

---

## 3. Database Schema

### Bảng `orders`

```sql
- order_id (PK)
- user_id (FK)
- staff_id (FK)
- order_date (DATETIME)
- total_amount (DECIMAL) -- Được tính toán
- status (VARCHAR) -- 'pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'
```

### Bảng `orderdetails`

```sql
- order_detail_id (PK)
- order_id (FK) -- Liên kết order
- variant_id (FK) -- Liên kết variant
- quantity (INT) -- Số lượng bán
- unit_price (DECIMAL) -- Giá khi mua
- discount_amount (DECIMAL)
```

### Bảng `products` & `productvariants`

```sql
products:
- product_id (PK)
- name
- category_id
- image_path
- original_price

productvariants:
- variant_id (PK)
- product_id (FK)
- size
- color
- current_price
- image_path
```

---

## 4. API Endpoints

### Marketing/Promotions API

| Endpoint                        | Method   | Mô Tả        |
| ------------------------------- | -------- | ------------ |
| `/admin/promotions`             | GET      | Danh sách mã |
| `/admin/promotions/add`         | GET/POST | Thêm mã      |
| `/admin/promotions/edit/{id}`   | GET/POST | Chỉnh sửa    |
| `/admin/promotions/delete-ajax` | POST     | Xóa (AJAX)   |

### Reporting API (Embedded in AdminController)

```php
// Trong AdminController@index()
$totalRevenue = $this->orderModel->getTotalRevenue();
$topProducts = $this->orderModel->getTopProducts(5);
```

---

## 5. Cách Sử Dụng

### Thêm Mã Khuyến Mãi (Admin)

1. Vào Admin → Mã Khuyến Mãi
2. Nhấn "+ Thêm Mã Mới"
3. Điền thông tin:
   - Mã: SUMMER30
   - Loại: Phần Trăm (%)
   - Giá trị: 30
   - Ngày bắt đầu: 01/01/2025 00:00
   - Ngày kết thúc: 31/01/2025 23:59
4. Nhấn "Tạo Mã Khuyến Mãi"

### Xem Báo Cáo (Admin)

1. Vào Admin → Dashboard
2. Xem 4 chỉ số chính (Sản phẩm, Đơn hàng, Người dùng, Doanh thu)
3. Xem Top 5 sản phẩm bán chạy
4. Nhấn "Truy cập nhanh" để quản lý

---

## 6. Troubleshooting

| Vấn Đề                | Nguyên Nhân             | Giải Pháp                              |
| --------------------- | ----------------------- | -------------------------------------- |
| Danh sách mã trống    | Chưa thêm mã            | Tạo mã từ "Thêm Mã Mới"                |
| Doanh thu hiển thị 0  | Không có đơn completed  | Cần có đơn hàng với status='completed' |
| Top 5 sản phẩm trống  | Không có orderdetails   | Cần có đơn hàng đã thanh toán          |
| Ngày kết thúc báo lỗi | Ngày kết thúc < bắt đầu | Chọn ngày kết thúc > bắt đầu           |

---

**Happy Reporting! 📈**
