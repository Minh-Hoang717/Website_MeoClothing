<!-- Login Page -->
<section class="auth-section">
    <div class="auth-card">
        <h1>🔑 Đăng Nhập</h1>
        <p class="subtitle">Chào mừng bạn quay lại!</p>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">✅ <?php echo htmlspecialchars($_SESSION['success']); ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="username">👤 Tên Đăng Nhập</label>
                <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
            </div>

            <div class="form-group">
                <label for="password">🔒 Mật Khẩu</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit" class="btn-submit">🚀 Đăng Nhập</button>
        </form>

        <div class="auth-footer">
            Chưa có tài khoản? <a href="<?php echo APP_URL; ?>/auth/register">Đăng ký ngay!</a>
        </div>
    </div>
</section>
