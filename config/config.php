<?php
// File: config/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // User mặc định của XAMPP/WAMP
define('DB_PASS', '');         // Pass mặc định thường để trống
define('DB_NAME', 'meo_clothingstore');

// URL gốc của dự án (Sửa lại theo port máy bạn)
define('BASE_URL', 'http://localhost/MeoClothingStore/public/');

// Tùy chọn kết nối PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
?>