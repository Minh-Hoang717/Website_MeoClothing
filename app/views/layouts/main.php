<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Thời Trang Dễ Thương</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <h1><a href="<?php echo APP_URL; ?>">🐱 Meow Clothing</a></h1>
                </div>
                <nav class="nav-main">
                    <ul>
                        <li><a href="<?php echo APP_URL; ?>">🏠 Trang Chủ</a></li>
                        <li><a href="<?php echo APP_URL; ?>/product/category/1">👕 Áo</a></li>
                        <li><a href="<?php echo APP_URL; ?>/product/category/2">👖 Quần</a></li>
                        <li><a href="<?php echo APP_URL; ?>/product/category/3">🎒 Phụ Kiện</a></li>
                    </ul>
                </nav>
                <div class="header-right">
                    <input type="text" class="search-box" id="searchBox" placeholder="🔍 Tìm kiếm sản phẩm...">
                    <a href="<?php echo APP_URL; ?>/cart/view" class="cart-link">
                        🛒 Giỏ hàng <span id="cartCount" class="badge">0</span>
                    </a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="user-menu">
                            <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?></span>
                            <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'staff'): ?>
                                <a href="<?php echo APP_URL; ?>/admin" class="admin-link">⚙️ Quản Trị</a>
                            <?php endif; ?>
                            <a href="<?php echo APP_URL; ?>/auth/logout" class="logout-link">🚪 Thoát</a>
                        </div>
                    <?php else: ?>
                        <div class="user-menu">
                            <a href="<?php echo APP_URL; ?>/auth/login" class="login-link">🔑 Đăng Nhập</a>
                            <a href="<?php echo APP_URL; ?>/auth/register" class="register-link">✨ Đăng Ký</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>🐱 Meow Clothing</h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; line-height: 1.8;">
                        Thời trang dễ thương dành cho bạn. Chất lượng tốt, giá cả phải chăng!
                    </p>
                </div>
                <div class="footer-section">
                    <h3>Liên Kết</h3>
                    <ul>
                        <li><a href="<?php echo APP_URL; ?>">Trang Chủ</a></li>
                        <li><a href="<?php echo APP_URL; ?>/product/category/1">Áo</a></li>
                        <li><a href="<?php echo APP_URL; ?>/product/category/2">Quần</a></li>
                        <li><a href="<?php echo APP_URL; ?>/product/category/3">Phụ Kiện</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Hỗ Trợ</h3>
                    <ul>
                        <li><a href="#">Hướng dẫn mua hàng</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Liên Hệ</h3>
                    <ul>
                        <li><a href="#">📍 123 Đường ABC, TP.HCM</a></li>
                        <li><a href="#">📞 0123 456 789</a></li>
                        <li><a href="#">✉️ info@meowclothing.vn</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 <span>Meow Clothing Store</span>. Made with 💖 in Vietnam</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo APP_URL; ?>/js/script.js"></script>
</body>
</html>
