# 📁 Cấu Trúc Hoàn Chỉnh Dự Án MVC

```
meo-clothing-store/
│
├── 📄 README.md                          # Hướng dẫn cài đặt & sử dụng
├── 📄 MODULES_GUIDE.md                  # ⭐ Hướng dẫn Module Marketing & Reporting
├── 📄 SUMMARY.md                        # ⭐ Tóm tắt module mới
├── 📄 FILE_STRUCTURE.md                 # File này
│
├── 📁 config/
│   └── 📄 config.php                    # Cấu hình database & app constants
│
├── 📁 DataBase/
│   └── 📄 meo_clothingstore.sql         # Schema SQL
│
├── 📁 public/                           # Document root
│   ├── 📄 index.php                     # Entry point (Router)
│   ├── 📄 .htaccess                     # URL rewriting
│   │
│   ├── 📁 css/
│   │   └── 📄 style.css                 # CSS chính (Responsive)
│   │
│   ├── 📁 js/
│   │   └── 📄 script.js                 # JavaScript (Fetch API)
│   │
│   └── 📁 uploads/                      # Thư mục upload ảnh sản phẩm
│       └── (ảnh tải lên sẽ ở đây)
│
└── 📁 app/                              # Application logic
    │
    ├── 📁 core/                         # Framework core
    │   ├── 📄 Database.php              # PDO Singleton connection
    │   ├── 📄 Controller.php            # Base controller (render, json, redirect)
    │   ├── 📄 Model.php                 # Base model (CRUD operations)
    │   └── 📄 Router.php                # Simple router (GET/POST/PUT/DELETE)
    │
    ├── 📁 models/                       # Business logic
    │   ├── 📄 Product.php               # Sản phẩm (getAll, getByCategory, search)
    │   ├── 📄 ProductVariant.php        # Biến thể (size, color, ảnh)
    │   ├── 📄 Inventory.php             # Tồn kho (isInStock, updateQuantity)
    │   ├── 📄 Order.php                 # Đơn hàng ⭐ (getTotalRevenue, getTopProducts)
    │   ├── 📄 User.php                  # Người dùng (authenticate, createUser)
    │   └── 📄 Promotion.php             # Mã khuyến mãi (getActiveByCode, calculateDiscount)
    │
    ├── 📁 controllers/                  # Request handlers
    │   ├── 📄 HomeController.php        # Trang chủ, danh sách, chi tiết
    │   ├── 📄 ProductController.php     # Danh sách theo danh mục
    │   ├── 📄 CartController.php        # Giỏ hàng (add, update, remove, promotion)
    │   ├── 📄 AuthController.php        # Đăng nhập, đăng ký, đăng xuất
    │   ├── 📄 AdminController.php       # Admin dashboard ⭐ (thêm thống kê)
    │   └── 📄 PromotionController.php   # ⭐ Quản lý mã khuyến mãi (CRUD)
    │
    └── 📁 views/                        # Templates
        ├── 📁 layouts/
        │   └── 📄 main.php              # Layout chính (header, footer, content)
        │
        ├── 📁 home/
        │   └── 📄 index.php             # Danh sách sản phẩm (grid + pagination)
        │
        ├── 📁 product/
        │   ├── 📄 detail.php            # Chi tiết sản phẩm (variant, inventory)
        │   └── 📄 category.php          # Danh sách theo danh mục
        │
        ├── 📁 cart/
        │   └── 📄 view.php              # Giỏ hàng (items + summary + promotion)
        │
        ├── 📁 auth/
        │   ├── 📄 login.php             # Form đăng nhập
        │   └── 📄 register.php          # Form đăng ký
        │
        └── 📁 admin/
            ├── 📄 index.php             # ⭐ Dashboard (stats + top products)
            ├── 📄 products.php          # Danh sách sản phẩm
            ├── 📄 add-product.php       # Form thêm sản phẩm (upload ảnh)
            ├── 📄 orders.php            # Quản lý đơn hàng
            │
            └── 📁 promotions/           # ⭐ PROMOTION VIEWS
                ├── 📄 index.php         # Danh sách mã (table + status badge)
                ├── 📄 add.php           # Form thêm mã (validation JS)
                └── 📄 edit.php          # Form chỉnh sửa mã
```

---

## 📊 Database Schema

```
┌─────────────────────────────────────────────────────┐
│                   DATABASES                         │
├─────────────────────────────────────────────────────┤
│                                                     │
│  users ─────┐                                       │
│             │                                       │
│  products   ├──→ orders ─→ orderdetails ←┐          │
│      │      │                            │          │
│      ├──→ productvariants ─────────────────→inventory
│      │      │                            │          │
│      │      └──→ payments                 │          │
│      │                                    │          │
│      └─→ categories                       │          │
│                                           │          │
│  promotions ────────────────────────────────┘        │
│                                                     │
└─────────────────────────────────────────────────────┘

Key Tables:
  - users: Người dùng (admin/staff/customer)
  - products: Sản phẩm chính
  - productvariants: Biến thể (size, color, ảnh)
  - inventory: Tồn kho
  - orders: Đơn hàng
  - orderdetails: Chi tiết từng dòng đơn
  - promotions: Mã khuyến mãi
  - payments: Thanh toán (optional)
  - categories: Danh mục
```

---

## 🔄 Request Flow

```
User Browser
    ↓
    ├─→ GET / ────────────→ public/index.php (Router)
    │                              ↓
    │                      Dispatch Request
    │                              ↓
    │                   HomeController@index()
    │                              ↓
    │                   Load Product Model
    │                              ↓
    │                   Query: SELECT * FROM products
    │                              ↓
    │                   Render: views/home/index.php
    │                              ↓
    ├─← HTML Response ─────────────┘
    │
    ├─→ POST /cart/add ──→ public/index.php
    │                              ↓
    │                   CartController@add()
    │                              ↓
    │                   Validate & add to $_SESSION
    │                              ↓
    ├─← JSON Response ──────────────┘
    │   {"success": true, "cartCount": 3}
```

---

## 🎯 Architecture Overview

```
PRESENTATION LAYER (Views)
  ├── layouts/main.php (Header/Footer)
  ├── home/ (Client pages)
  ├── auth/ (Login/Register)
  ├── cart/ (Shopping)
  └── admin/ (Management)
         └── promotions/ ⭐ NEW

        ↓ (Dependency Injection)

BUSINESS LOGIC LAYER (Controllers)
  ├── HomeController
  ├── ProductController
  ├── CartController
  ├── AuthController
  ├── AdminController
  └── PromotionController ⭐ NEW

        ↓ (Model usage)

DATA ACCESS LAYER (Models)
  ├── Product
  ├── ProductVariant
  ├── Inventory
  ├── Order ⭐ (getTotalRevenue, getTopProducts)
  ├── User
  └── Promotion

        ↓ (PDO queries)

DATABASE LAYER (MySQL)
  ├── users, products, orders
  ├── orderdetails, inventory
  ├── promotions, categories
  └── productvariants

        ↓ (Core framework)

INFRASTRUCTURE
  ├── Database.php (PDO Singleton)
  ├── Controller.php (Base class)
  ├── Model.php (CRUD operations)
  └── Router.php (URL routing)
```

---

## 🛣️ Route Map

### CLIENT ROUTES

```
GET   /                          → Home (danh sách sản phẩm)
GET   /home/detail/{id}          → Chi tiết sản phẩm
GET   /home/search               → Tìm kiếm (API)
GET   /home/get-variants/{id}    → Lấy variants (API)
GET   /product/category/{id}     → Danh mục
GET   /cart/view                 → Xem giỏ hàng
POST  /cart/add                  → Thêm vào giỏ (AJAX)
POST  /cart/apply-promotion      → Áp dụng mã (AJAX)
GET   /auth/login                → Form đăng nhập
POST  /auth/login                → Xử lý đăng nhập
GET   /auth/logout               → Đăng xuất
```

### ADMIN ROUTES

```
GET   /admin                     → Dashboard ⭐ (stats + top 5)
GET   /admin/products            → Danh sách sản phẩm
GET   /admin/add-product         → Form thêm
POST  /admin/add-product         → Xử lý upload
GET   /admin/orders              → Quản lý đơn
GET   /admin/promotions          → ⭐ Danh sách mã
GET   /admin/promotions/add      → ⭐ Form thêm mã
POST  /admin/promotions/add      → ⭐ Xử lý tạo mã
GET   /admin/promotions/edit/{id} → ⭐ Form sửa
POST  /admin/promotions/edit/{id} → ⭐ Xử lý sửa
POST  /admin/promotions/delete-ajax → ⭐ Xóa (AJAX)
```

---

## 🎨 CSS Classes & Styling

```css
/* Layouts */
.admin-section      → Grid 2 cột (sidebar + content)
.admin-sidebar      → Navigation menu
.admin-content      → Main content area
.admin-header       → Title + action buttons

/* Stats */
.stats-grid         → Grid layout (auto-fit)
.stat-card          → Individual stat card
.stat-blue/green/purple/orange → Gradient backgrounds

/* Tables */
.admin-table        → Product/promotion tables
.report-table       → Dashboard report table
.action-buttons     → Edit/Delete buttons

/* Forms */
.admin-form         → Product/promotion forms
.form-group         → Input wrapper
.form-row           → Multi-column layout
.input-with-unit    → Input + % or đ unit

/* Components */
.badge              → Small labels
.rank-badge         → Numbered ranks (1-5)
.status-badge       → Active/Pending/Expired
.quick-link         → Quick access cards;
```

---

## 🚀 Performance Optimizations

```
✅ PDO Prepared Statements → Prevent SQL injection
✅ Singleton Database → Single connection reuse
✅ Base Model → Reusable CRUD methods
✅ Lazy Loading → Load data only when needed
✅ Caching Session → Store cart in $_SESSION
✅ Responsive CSS → Mobile-first design
✅ AJAX Requests → No full page reload
✅ Indexed Queries → ORDER BY total_sold DESC
✅ GROUP BY Aggregation → Calculate totals efficiently
```

---

## 📝 Development Guidelines

```
1. Controllers
   - Extend core\Controller
   - Use dependency injection in constructor
   - Always call checkAdmin() for protected routes
   - Return JSON for APIs, render() for views

2. Models
   - Extend core\Model
   - Set $table property
   - Use $this->db->prepare() for queries
   - Return results via single() or getAll()

3. Views
   - Use htmlspecialchars() to escape output
   - Extract variables with extract($data)
   - Use consistent indentation & formatting
   - Include CSS in <style> tags

4. Routing
   - Define in public/index.php
   - Use controller@method format
   - Support route parameters: {id}, {categoryId}
   - Match HTTP method (GET/POST/PUT/DELETE)

5. Forms
   - Use method="POST" & enctype if file upload
   - Include validation on client & server
   - Show errors array in view
   - Redirect after successful submit
```

---

## ✨ Next Steps (Optional)

```
🔜 Payment Integration (Stripe/VNPay)
🔜 Email Notifications (PHPMailer)
🔜 Image Optimization (Thumbs)
🔜 Search Filters (Advanced)
🔜 Product Reviews (Rating)
🔜 Admin Charts (Chart.js)
🔜 Export Reports (Excel/PDF)
🔜 API Documentation (Swagger)
```

---

**Project Status: ✅ COMPLETE & PRODUCTION-READY** 🎊
