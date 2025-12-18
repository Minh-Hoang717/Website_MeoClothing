# API Documentation - Meo Clothing Store

## Overview

Complete REST API documentation for Meo Clothing Store e-commerce system. Backend built with PHP, MySQL database, VNPay payment gateway integration.

**Base URL:** `http://localhost/api`  
**Current Version:** v1

---

## Table of Contents

1. [Promotions API](#promotions-api)
2. [Payments API](#payments-api)
3. [Reports API](#reports-api)
4. [Error Handling](#error-handling)
5. [Authentication](#authentication)
6. [Testing](#testing)

---

## Promotions API

### Module 5: Marketing & Promotions Management

#### GET /api/promotions

List all promotions with optional filtering.

**Parameters:**

- `filter` (optional): `all` | `active` | `upcoming` | `expired` (default: `all`)
- `page` (optional): Page number (default: 1)
- `pageSize` (optional): Items per page (default: 20, max: 100)

**Response:**

```json
{
  "success": true,
  "message": "Lấy danh sách khuyến mãi thành công",
  "data": [
    {
      "promotion_id": 1,
      "code": "SUMMER10",
      "discount_type": "percentage",
      "discount_value": 10,
      "start_date": "2025-06-01 00:00:00",
      "end_date": "2025-08-31 23:59:59"
    }
  ],
  "pagination": {
    "total": 5,
    "page": 1,
    "pageSize": 20,
    "totalPages": 1
  }
}
```

#### GET /api/promotions/{id}

Get specific promotion by ID.

**Response:**

```json
{
  "success": true,
  "message": "Lấy thông tin khuyến mãi thành công",
  "data": {
    "promotion_id": 1,
    "code": "SUMMER10",
    "discount_type": "percentage",
    "discount_value": 10,
    "start_date": "2025-06-01 00:00:00",
    "end_date": "2025-08-31 23:59:59"
  }
}
```

#### POST /api/promotions

Create new promotion (Admin only).

**Headers:**

```
Authorization: Bearer <admin_token>
Content-Type: application/json
```

**Request Body:**

```json
{
  "code": "SUMMER10",
  "discount_type": "percentage",
  "discount_value": 10,
  "start_date": "2025-06-01 00:00:00",
  "end_date": "2025-08-31 23:59:59"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tạo khuyến mãi thành công",
  "data": { ... },
  "statusCode": 201
}
```

#### PUT /api/promotions/{id}

Update promotion (Admin only).

**Headers:**

```
Authorization: Bearer <admin_token>
Content-Type: application/json
```

**Request Body:** (same as POST, all fields optional)

#### DELETE /api/promotions/{id}

Delete promotion (Admin only).

**Headers:**

```
Authorization: Bearer <admin_token>
```

#### GET /api/promotions/validate?code=XXX

Validate if promo code is active and valid.

**Response:**

```json
{
  "success": true,
  "data": {
    "valid": true,
    "promotion": { ... }
  }
}
```

#### POST /api/promotions/apply

Apply promo code and calculate discount.

**Request Body:**

```json
{
  "code": "SUMMER10",
  "total_amount": 500000
}
```

**Response:**

```json
{
  "success": true,
  "message": "Áp dụng mã khuyến mãi thành công",
  "data": {
    "promotion": { ... },
    "total_amount": 500000,
    "discount_amount": 50000,
    "final_amount": 450000
  }
}
```

---

## Payments API

### Module 2: Payment Processing & VNPay Integration

#### GET /api/payments

Get all payments (Admin only).

**Headers:**

```
Authorization: Bearer <admin_token>
```

**Parameters:**

- `page` (optional): Page number (default: 1)
- `pageSize` (optional): Items per page (default: 20)

#### GET /api/payments/{id}

Get payment details by ID.

**Response:**

```json
{
  "success": true,
  "data": {
    "payment_id": 1,
    "order_id": 123,
    "payment_date": "2025-12-16 10:30:00",
    "payment_method": "VNPay - VCCB",
    "amount": 450000,
    "transaction_code": "VNP20251216103001"
  }
}
```

#### POST /api/payments/process

Create payment and get VNPay redirect URL.

**Request Body:**

```json
{
  "order_id": 123
}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "payment_url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?...",
    "txn_ref": "123_1702734600",
    "order_id": 123,
    "amount": 500000
  }
}
```

#### GET /api/payments/vnpay-return

VNPay callback redirect (user returns from payment).

**Query Parameters:** (sent by VNPay)

- `vnp_ResponseCode`: Response code from VNPay
- `vnp_TxnRef`: Transaction reference
- `vnp_Amount`: Amount (in 100 VND)
- `vnp_BankCode`: Bank code
- `vnp_SecureHash`: Security hash

**Behavior:**

- Validates secure hash
- Records payment if successful (ResponseCode = '00')
- Redirects to `/payment-success.html` or `/payment-error.html`

#### POST /api/payments/vnpay-ipn

VNPay IPN webhook (server-to-server notification).

**Response:**

```json
{
  "RspCode": "00",
  "Message": "Confirm Success"
}
```

---

## Reports API

### Module 4: Reporting & Analytics

#### GET /api/reports/revenue

Revenue report by time period.

**Parameters:**

- `group_by` (optional): `daily` | `monthly` | `yearly` (default: `daily`)
- `start` (optional): Start date (YYYY-MM-DD)
- `end` (optional): End date (YYYY-MM-DD)

**Response:**

```json
{
  "success": true,
  "data": {
    "group_by": "daily",
    "start": "2025-11-16 00:00:00",
    "end": "2025-12-16 23:59:59",
    "rows": [
      {
        "period": "2025-12-16",
        "total_revenue": 1500000,
        "transactions": 3
      }
    ]
  }
}
```

#### GET /api/reports/customers

Customer analytics.

**Parameters:**

- `metric` (optional): `summary` | `top_spenders` (default: `summary`)
- `start` (optional): Start date
- `end` (optional): End date
- `limit` (optional): Limit results (default: 10)

**Response (summary):**

```json
{
  "success": true,
  "data": {
    "metric": "summary",
    "summary": {
      "new_customers": 15,
      "active_customers": 42,
      "revenue": 5000000
    }
  }
}
```

**Response (top_spenders):**

```json
{
  "success": true,
  "data": {
    "metric": "top_spenders",
    "rows": [
      {
        "customer_id": 5,
        "full_name": "Nguyễn Văn A",
        "email": "a@example.com",
        "total_spent": 2000000,
        "payments": 4
      }
    ]
  }
}
```

#### GET /api/reports/products

Product performance report.

**Parameters:**

- `metric` (optional): `best_selling` | `low_stock` | `most_stock` (default: `best_selling`)
- `start` (optional): Start date (for best_selling)
- `end` (optional): End date (for best_selling)
- `limit` (optional): Limit results (default: 10)

**Response (best_selling):**

```json
{
  "success": true,
  "data": {
    "metric": "best_selling",
    "rows": [
      {
        "product_id": 3,
        "name": "Classic Black Jacket",
        "total_quantity": 25,
        "total_revenue": 5000000
      }
    ]
  }
}
```

**Response (low_stock):**

```json
{
  "success": true,
  "data": {
    "metric": "low_stock",
    "rows": [
      {
        "product_id": 1,
        "name": "T-Shirt",
        "variant_id": 10,
        "size": "M",
        "color": "Red",
        "quantity": 2
      }
    ]
  }
}
```

---

## Error Handling

All errors return with appropriate HTTP status codes.

### Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "timestamp": "2025-12-16 14:30:00"
}
```

### Validation Error Response

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "code": "Mã khuyến mãi is required",
    "discount_value": "Giá trị giảm must be a valid decimal number"
  }
}
```

### Status Codes

| Code | Meaning                               |
| ---- | ------------------------------------- |
| 200  | OK                                    |
| 201  | Created                               |
| 400  | Bad Request                           |
| 401  | Unauthorized                          |
| 403  | Forbidden                             |
| 404  | Not Found                             |
| 409  | Conflict (e.g., duplicate promo code) |
| 422  | Validation Error                      |
| 500  | Server Error                          |

---

## Authentication

Admin endpoints require Bearer token authentication.

### Header Format

```
Authorization: Bearer <token>
```

**Current Implementation:** Simple bearer token validation (to be enhanced with JWT in production).

### Admin Endpoints

- `POST /api/promotions` - Create promotion
- `PUT /api/promotions/{id}` - Update promotion
- `DELETE /api/promotions/{id}` - Delete promotion
- `GET /api/payments` - List all payments

---

## Testing

### Database Connectivity

Test database connection and view table information:

```
GET /api/test-db.php
```

### Quick Test Endpoints

#### 1. Test API Server

```
curl http://localhost/api/test
```

Expected: `{"success": true, "message": "API is working!"}`

#### 2. List All Promotions

```
curl http://localhost/api/promotions
```

#### 3. Get Active Promotions Only

```
curl "http://localhost/api/promotions?filter=active"
```

#### 4. Create a Promotion (requires admin token)

```
curl -X POST http://localhost/api/promotions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer admin_token" \
  -d '{
    "code": "NEWCODE",
    "discount_type": "percentage",
    "discount_value": 15,
    "start_date": "2025-12-20 00:00:00",
    "end_date": "2025-12-31 23:59:59"
  }'
```

#### 5. Apply Promo Code

```
curl -X POST http://localhost/api/promotions/apply \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SUMMER10",
    "total_amount": 500000
  }'
```

#### 6. Revenue Report

```
curl "http://localhost/api/reports/revenue?group_by=daily"
```

#### 7. Customer Report

```
curl "http://localhost/api/reports/customers?metric=top_spenders&limit=5"
```

#### 8. Product Report

```
curl "http://localhost/api/reports/products?metric=best_selling"
```

### Frontend Testing

1. **Admin Panel** - Manage promotions:

   - Open `http://localhost/admin/promotions.html`
   - Paste admin token (any string for dev)
   - Create, update, delete promotions
   - Test promo code validation

2. **Checkout Page** - Payment flow:

   - Open `http://localhost/checkout.html`
   - Apply promo code
   - Click "Thanh toán ngay"
   - Redirects to VNPay sandbox

3. **Payment Success/Error Pages**:
   - `http://localhost/payment-success.html`
   - `http://localhost/payment-error.html`

---

## Configuration

### VNPay Setup (Required for Production)

Edit `/api/config/config.php`:

```php
define('VNPAY_TMN_CODE', 'YOUR_TMN_CODE');
define('VNPAY_HASH_SECRET', 'YOUR_HASH_SECRET');
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); // Sandbox
// For production: 'https://pay.vnpayment.vn/paymentv2/vpcpay.html'
define('VNPAY_RETURN_URL', 'http://your-domain.com/api/payments/vnpay-return');
```

### Database Configuration

Edit `/api/config/database.php`:

```php
private $host = "127.0.0.1:3306";
private $db_name = "meo_clothingstore";
private $username = "root";
private $password = "";
```

---

## API Endpoints Summary

| Method | Endpoint                 | Auth | Description      |
| ------ | ------------------------ | ---- | ---------------- |
| GET    | /api/promotions          | No   | List promotions  |
| GET    | /api/promotions/{id}     | No   | Get promotion    |
| POST   | /api/promotions          | Yes  | Create promotion |
| PUT    | /api/promotions/{id}     | Yes  | Update promotion |
| DELETE | /api/promotions/{id}     | Yes  | Delete promotion |
| GET    | /api/promotions/validate | No   | Validate code    |
| POST   | /api/promotions/apply    | No   | Apply code       |
| GET    | /api/payments            | Yes  | List payments    |
| GET    | /api/payments/{id}       | No   | Get payment      |
| POST   | /api/payments/process    | No   | Create payment   |
| GET    | /api/reports/revenue     | No   | Revenue report   |
| GET    | /api/reports/customers   | No   | Customer report  |
| GET    | /api/reports/products    | No   | Product report   |

---

## Change Log

### v1.0 (2025-12-16)

- Initial API release
- Promotions management (CRUD)
- VNPay payment integration
- Reporting & analytics
- Admin dashboard for promotions

---

## Support

For issues or questions:

- Check database connectivity: `/api/test-db.php`
- Review error responses for validation details
- Ensure admin token is properly formatted in Authorization header
- Verify VNPay credentials for payment functionality
