# Meo Clothing Store - Setup & Deployment Guide

## 📋 Project Overview

Complete e-commerce platform for clothing store with:

- Product catalog & shopping cart
- Promo code management
- VNPay payment gateway integration
- Admin dashboard
- Reporting & analytics

**Tech Stack:**

- Frontend: HTML5, CSS3, JavaScript (jQuery compatible)
- Backend: PHP 7.4+
- Database: MySQL 5.7+
- Payment: VNPay (sandbox & production)

---

## 🚀 Quick Start

### Prerequisites

- PHP 7.4+ with PDO MySQL support
- MySQL 5.7+ or higher
- Web server (Apache with mod_rewrite or Nginx)
- VNPay merchant account (for payment testing)

### 1. Database Setup

**Option A: Using phpMyAdmin**

1. Create new database: `meo_clothingstore`
2. Import `DataBase/meo_clothingstore.sql`
3. Tables created: categories, customers, employees, inventory, orderdetails, orders, payments, products, productvariants, promotions

**Option B: Using MySQL CLI**

```bash
mysql -u root -p < DataBase/meo_clothingstore.sql
```

### 2. Configure Database Connection

Edit `/api/config/database.php`:

```php
private $host = "127.0.0.1:3306";      // Your MySQL host
private $db_name = "meo_clothingstore"; // Database name
private $username = "root";             // MySQL username
private $password = "";                 // MySQL password
```

### 3. Configure VNPay (Optional for Testing)

Edit `/api/config/config.php`:

```php
define('VNPAY_TMN_CODE', 'YOUR_TMN_CODE');
define('VNPAY_HASH_SECRET', 'YOUR_HASH_SECRET');
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
define('VNPAY_RETURN_URL', 'http://localhost/api/payments/vnpay-return');
```

### 4. Verify Setup

Open browser: `http://localhost/api/test-db.php`

Should see:

```json
{
  "success": true,
  "database": {
    "status": "connected"
  },
  "tables": [
    { "name": "categories", "rows": 0, ... },
    { "name": "products", "rows": 0, ... },
    ...
  ]
}
```

### 5. Access Application

| URL                                      | Purpose           |
| ---------------------------------------- | ----------------- |
| `http://localhost/`                      | Frontend homepage |
| `http://localhost/shop.html`             | Product listing   |
| `http://localhost/checkout.html`         | Checkout page     |
| `http://localhost/admin/promotions.html` | Admin promotions  |

---

## 📁 Directory Structure

```
ultras-1.0.0/
├── api/                          # Backend API
│   ├── config/
│   │   ├── config.php           # App config (VNPay, JWT, etc)
│   │   └── database.php         # Database connection
│   ├── controllers/
│   │   ├── PromotionController.php
│   │   ├── PaymentController.php
│   │   └── ReportController.php
│   ├── models/
│   │   ├── BaseModel.php
│   │   ├── Promotion.php
│   │   ├── Payment.php
│   │   ├── Order.php
│   ├── services/
│   │   └── VNPayService.php
│   ├── utils/
│   │   ├── Response.php
│   │   ├── Validator.php
│   ├── middleware/
│   │   ├── CORS.php
│   │   └── Auth.php
│   ├── index.php                # Main router
│   ├── test-db.php              # DB connectivity test
│   ├── test-suite.php           # API test suite
│   └── .htaccess                # URL rewriting
├── admin/
│   ├── promotions.html          # Admin promotions panel
│   └── js/
│       └── promotions.js        # Admin logic
├── js/
│   ├── cart.js                  # Checkout logic
│   ├── script.js
│   └── jquery-1.11.0.min.js
├── css/
│   ├── normalize.css
│   ├── vendor.css
│   └── style.css
├── DataBase/
│   └── meo_clothingstore.sql    # Database dump
├── checkout.html                # Checkout page
├── payment-success.html         # Success page
├── payment-error.html           # Error page
├── API_DOCUMENTATION.md         # This API docs
├── SETUP.md                     # Setup guide (this file)
└── index.html                   # Homepage
```

---

## 🔌 API Endpoints

### Promotions

- `GET /api/promotions` - List promotions
- `POST /api/promotions` - Create (admin)
- `PUT /api/promotions/{id}` - Update (admin)
- `DELETE /api/promotions/{id}` - Delete (admin)
- `POST /api/promotions/apply` - Apply code
- `GET /api/promotions/validate?code=XXX` - Validate code

### Payments

- `GET /api/payments` - List (admin)
- `GET /api/payments/{id}` - Get payment
- `POST /api/payments/process` - Create payment
- `GET /api/payments/vnpay-return` - VNPay callback

### Reports

- `GET /api/reports/revenue` - Revenue report
- `GET /api/reports/customers` - Customer stats
- `GET /api/reports/products` - Product stats

---

## 🧪 Testing

### Test Database

```bash
curl http://localhost/api/test-db.php
```

### Test Promotion Creation

```bash
curl -X POST http://localhost/api/promotions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer test-token" \
  -d '{
    "code": "TEST10",
    "discount_type": "percentage",
    "discount_value": 10,
    "start_date": "2025-12-16 00:00:00",
    "end_date": "2025-12-31 23:59:59"
  }'
```

### Test Apply Promo

```bash
curl -X POST http://localhost/api/promotions/apply \
  -H "Content-Type: application/json" \
  -d '{
    "code": "TEST10",
    "total_amount": 500000
  }'
```

### Admin Panel

1. Open `http://localhost/admin/promotions.html`
2. Paste any token in "Admin Token" field
3. Click "Lưu Token"
4. Create promotions, view list, test codes

### Checkout Flow

1. Open `http://localhost/checkout.html`
2. Fill customer info
3. Apply promo code (if created in admin)
4. Click "Thanh toán ngay"
5. Redirects to VNPay sandbox (if credentials set)

---

## 📊 Database Schema

### Main Tables

**promotions**

- promotion_id (PK)
- code (unique, VARCHAR 20)
- discount_type (ENUM: percentage, fixed)
- discount_value (DECIMAL 10,2)
- start_date (DATETIME)
- end_date (DATETIME)

**orders**

- order_id (PK)
- customer_id (FK)
- employee_id (FK, nullable)
- order_date (DATETIME)
- total_amount (DECIMAL 10,2)
- status (VARCHAR 20: pending, confirmed, processing, shipping, completed, cancelled)

**payments**

- payment_id (PK)
- order_id (FK, unique)
- payment_date (DATETIME)
- payment_method (VARCHAR 50)
- amount (DECIMAL 10,2)
- transaction_code (unique, nullable)

**products & productvariants**

- Products have multiple variants (size, color, price combinations)
- Inventory tracks stock per variant

---

## 🔐 Security Notes

### Development

- CORS allows all origins in dev mode
- Simple bearer token (no JWT validation yet)
- Password stored in plain text in config (change in production)

### Production Checklist

- [ ] Enable HTTPS
- [ ] Implement proper JWT authentication
- [ ] Hash admin passwords
- [ ] Set strong database password
- [ ] Update VNPay to production endpoint
- [ ] Configure proper CORS whitelist
- [ ] Enable database backups
- [ ] Add rate limiting
- [ ] Use environment variables for secrets

---

## 🐛 Troubleshooting

### Database Connection Failed

1. Check MySQL is running
2. Verify database name matches config
3. Check username/password in database.php
4. Test: `mysql -u root -p meo_clothingstore`

### API Returns 404

1. Ensure Apache mod_rewrite is enabled
2. Check .htaccess file in `/api/` folder
3. Try direct URL: `http://localhost/api/index.php?route=promotions`

### Payment Not Working

1. Check VNPay credentials in config.php
2. Verify VNPAY_RETURN_URL matches your domain
3. Test with VNPay sandbox first
4. Check `/api/test-db.php` to ensure DB is connected

### Admin Token Not Working

1. Any token string works in dev (no validation)
2. For production, implement proper JWT
3. Token goes in `Authorization: Bearer <token>` header

---

## 📝 Next Steps

### To Implement

- [ ] User authentication (login/register)
- [ ] Shopping cart persistence (session/localStorage)
- [ ] Order creation endpoint
- [ ] Email notifications
- [ ] Admin dashboard (orders, customers)
- [ ] Product management
- [ ] Inventory tracking
- [ ] Wishlist functionality

### To Enhance

- [ ] Add product images
- [ ] Implement real JWT auth
- [ ] Add API rate limiting
- [ ] Implement caching layer
- [ ] Add email notifications
- [ ] Create mobile app
- [ ] Add SMS notifications

---

## 📞 Support

For issues or questions:

1. Check `API_DOCUMENTATION.md` for endpoint details
2. Run `/api/test-db.php` to verify database
3. Review error messages in API responses
4. Check browser console for frontend errors
5. Review Apache/PHP error logs

---

## 📄 License

Free HTML template from TemplatesJungle.com (see readme.txt)

**Modified for:** E-commerce with API backend, promotions, and payment gateway integration.
