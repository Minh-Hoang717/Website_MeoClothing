# 🐱 Meow Clothing Store - MVC PHP Website

Một website bán quần áo được xây dựng hoàn toàn bằng **PHP OOP**, mô hình **MVC**, và **MySQL Database**.

---

## 📋 Yêu Cầu Hệ Thống

- **PHP**: >= 7.4
- **MySQL**: 5.7 hoặc cao hơn
- **Web Server**: Apache (với mod_rewrite enabled)
- **cURL** & **JSON** extensions (thường có sẵn)

---

## 🚀 Cài Đặt & Cấu Hình

### 1. **Thiết Lập Database**

```sql
-- 1. Tạo database
CREATE DATABASE meo_clothingstore CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

-- 2. Import file SQL
-- Mở phpMyAdmin hoặc dòng lệnh:
mysql -u root -p meo_clothingstore < DataBase/meo_clothingstore.sql

-- 3. Thêm cột image_path cho 2 bảng (ĐẠO TRỌNG!)
ALTER TABLE `products` ADD COLUMN `image_path` VARCHAR(255) DEFAULT NULL AFTER `original_price`;
ALTER TABLE `productvariants` ADD COLUMN `image_path` VARCHAR(255) DEFAULT NULL AFTER `current_price`;
```

### 2. **Cấu Hình File Config**

Mở file `config/config.php` và điều chỉnh:

```php
define('DB_HOST', '127.0.0.1');     // Host MySQL
define('DB_PORT', '3306');          // Port MySQL
define('DB_NAME', 'meo_clothingstore');
define('DB_USER', 'root');          // Username MySQL
define('DB_PASS', '');              // Password MySQL
define('APP_URL', 'http://localhost/Meow_Clothing_Store');
```

### 3. **Cấu Hình Apache & .htaccess**

**File: `public/.htaccess`** (đã có sẵn)

Bật `mod_rewrite` trong Apache:

```bash
# Windows/XAMPP
# Mở httpd.conf, tìm và uncomment dòng:
# LoadModule rewrite_module modules/mod_rewrite.so
```

### 4. **Phân Quyền Thư Mục**

```bash
# Linux/Mac
chmod 755 public/
chmod 777 public/uploads/

# Windows: Click chuột phải > Properties > Security
# Cho Full Control cho Authenticated Users
```

---

## 📁 Cấu Trúc Thư Mục

```
meo-clothing-store/
├── public/
│   ├── index.php                 # Entry point
│   ├── uploads/                  # Lưu ảnh upload
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── .htaccess                 # URL rewriting
├── app/
│   ├── core/
│   │   ├── Database.php          # Singleton PDO connection
│   │   ├── Controller.php        # Base controller
│   │   ├── Model.php             # Base model
│   │   └── Router.php            # Router
│   ├── controllers/
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── AuthController.php
│   │   └── AdminController.php
│   ├── models/
│   │   ├── Product.php
│   │   ├── ProductVariant.php
│   │   ├── Inventory.php
│   │   ├── Order.php
│   │   ├── User.php
│   │   └── Promotion.php
│   └── views/
│       ├── layouts/
│       │   └── main.php
│       ├── home/
│       │   └── index.php
│       ├── product/
│       │   ├── detail.php
│       │   └── category.php
│       ├── cart/
│       │   └── view.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       └── admin/
│           ├── index.php
│           ├── add-product.php
│           └── orders.php
├── config/
│   └── config.php
├── DataBase/
│   └── meo_clothingstore.sql
└── README.md
```

---

## 🔌 Routes & API

### **Client Routes**

| Route                            | Method   | Mô Tả                          |
| -------------------------------- | -------- | ------------------------------ |
| `/`                              | GET      | Trang chủ - Danh sách sản phẩm |
| `/home/detail/{id}`              | GET      | Chi tiết sản phẩm              |
| `/home/search`                   | GET      | Tìm kiếm sản phẩm              |
| `/home/get-variants/{id}`        | GET      | API lấy variants của sản phẩm  |
| `/product/category/{categoryId}` | GET      | Sản phẩm theo danh mục         |
| `/cart/view`                     | GET      | Xem giỏ hàng                   |
| `/cart/add`                      | POST     | Thêm vào giỏ (API)             |
| `/cart/update`                   | POST     | Cập nhật giỏ (API)             |
| `/cart/remove`                   | POST     | Xoá khỏi giỏ (API)             |
| `/cart/apply-promotion`          | POST     | Áp dụng mã khuyến mãi (API)    |
| `/auth/login`                    | GET/POST | Đăng nhập                      |
| `/auth/register`                 | GET/POST | Đăng ký                        |
| `/auth/logout`                   | GET      | Đăng xuất                      |

### **Admin Routes**

| Route                        | Method   | Mô Tả                   |
| ---------------------------- | -------- | ----------------------- |
| `/admin`                     | GET      | Dashboard               |
| `/admin/products`            | GET      | Danh sách sản phẩm      |
| `/admin/add-product`         | GET/POST | Thêm sản phẩm           |
| `/admin/orders`              | GET      | Quản lý đơn hàng        |
| `/admin/update-order-status` | POST     | Cập nhật trạng thái đơn |

---

## 👤 Tài Khoản Admin Mẫu

**Tạo tài khoản admin trong DB:**

```sql
INSERT INTO users (username, password, full_name, email, role, created_at)
VALUES ('admin1', '$2y$10$...', 'Admin User', 'admin@store.com', 'admin', NOW());
```

Password hash (bcrypt):

- Username: `admin1`
- Password: `admin1` (hash: `$2y$10$N9qo8uLOickgx2ZMRZoMye4SAYy7wGvC8VkFTZhD9nfSPdPg.d2nW`)

---

## 🛠️ Công Nghệ Sử Dụng

| Thành Phần             | Công Nghệ               |
| ---------------------- | ----------------------- |
| **Backend**            | PHP 7.4+ (OOP)          |
| **Architecture**       | MVC Pattern             |
| **Database**           | MySQL 5.7+ (PDO)        |
| **Frontend**           | HTML5, CSS3, Vanilla JS |
| **API Communication**  | Fetch API (JSON)        |
| **Session Management** | PHP Session             |
| **Password Hashing**   | BCrypt                  |

---

## 📊 Cấu Trúc Database

### **Bảng chính:**

- `users` - Người dùng (Admin/Staff/Customer)
- `products` - Sản phẩm
- `productvariants` - Biến thể sản phẩm (kích cỡ, màu)
- `inventory` - Tồn kho
- `orders` - Đơn hàng
- `orderdetails` - Chi tiết đơn hàng
- `payments` - Thanh toán
- `promotions` - Mã khuyến mãi
- `categories` - Danh mục

---

## 🔐 Tính Năng Bảo Mật

✅ Password hashing với BCrypt
✅ PDO Prepared Statements (chống SQL injection)
✅ Session-based authentication
✅ Role-based access control (RBAC)
✅ Input validation & sanitization
✅ CSRF protection ready

---

## 📝 Hướng Dẫn Sử Dụng

### **1. Thêm Sản Phẩm (Admin)**

```
1. Đăng nhập admin (/auth/login)
2. Vào Admin Panel (/admin)
3. Chọn "Thêm Sản Phẩm"
4. Điền thông tin & upload ảnh
5. Ảnh sẽ lưu vào thư mục public/uploads/
6. Tên file sẽ lưu vào cột image_path
```

### **2. Mua Hàng (Customer)**

```
1. Duyệt sản phẩm từ trang chủ
2. Chọn chi tiết sản phẩm
3. Chọn kích cỡ & màu (image_path sẽ thay đổi)
4. Thêm vào giỏ hàng
5. Xem giỏ & áp dụng mã khuyến mãi
6. Thanh toán
```

---

## 🚨 Lỗi Thường Gặp & Cách Khắc Phục

| Lỗi                        | Nguyên Nhân                        | Cách Khắc Phục                       |
| -------------------------- | ---------------------------------- | ------------------------------------ |
| 404 Not Found              | URL rewriting chưa bật             | Bật mod_rewrite & kiểm tra .htaccess |
| Database Connection Failed | Config sai                         | Kiểm tra config/config.php           |
| File Upload Failed         | Thư mục uploads không có quyền ghi | `chmod 777 public/uploads/`          |
| Session Error              | Không gọi `session_start()`        | Check config.php line 31             |

---

## 📞 Hỗ Trợ & Liên Hệ

Nếu có vấn đề, vui lòng kiểm tra:

1. PHP version (>= 7.4)
2. MySQL connection
3. Folder permissions
4. Mod_rewrite enabled
5. .htaccess configuration

---

## 📄 License

Đây là dự án học tập. Tự do sử dụng và modify.

---

**Happy Coding! 🚀**
