<?php
/**
 * Application Configuration
 */

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// CORS Settings
define('ALLOWED_ORIGINS', ['http://localhost', 'http://127.0.0.1']);

// API Settings
define('API_VERSION', 'v1');
define('API_BASE_URL', '/api');

// VNPay Configuration
define('VNPAY_TMN_CODE', 'YOUR_TMN_CODE'); // Mã website tại VNPay
define('VNPAY_HASH_SECRET', 'YOUR_HASH_SECRET'); // Chuỗi bí mật
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); // URL thanh toán (sandbox)
define('VNPAY_RETURN_URL', 'http://localhost/api/payments/vnpay-return'); // URL trả về sau thanh toán
define('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'); // API query

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Pagination
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// Error Reporting (Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security
define('JWT_SECRET', 'your-secret-key-change-in-production'); // Đổi khi deploy
define('JWT_EXPIRATION', 86400); // 24 hours

// File Upload
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', __DIR__ . '/../../uploads/');

// Email Configuration (Optional - for marketing module)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_FROM_EMAIL', 'noreply@meostore.com');
define('SMTP_FROM_NAME', 'Meo Clothing Store');
?>
