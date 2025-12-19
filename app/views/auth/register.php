<!-- Register Page -->
<section class="auth-section">
    <div class="auth-card">
        <h1>✨ Đăng Ký</h1>
        <p class="subtitle">Tạo tài khoản để mua sắm dễ dàng hơn!</p>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>⚠️ Có lỗi xảy ra:</strong>
                <ul style="margin: 10px 0 0 20px; text-align: left;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="full_name">📝 Họ và Tên</label>
                <input type="text" id="full_name" name="full_name" 
                       placeholder="VD: Nguyễn Văn A"
                       value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="username">👤 Tên Đăng Nhập</label>
                <input type="text" id="username" name="username" 
                       placeholder="VD: nguyenvana"
                       value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" 
                       placeholder="VD: email@example.com"
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">🔒 Mật Khẩu</label>
                <input type="password" id="password" name="password" 
                       placeholder="Tối thiểu 6 ký tự" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">🔒 Xác Nhận Mật Khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="btn-submit">🎉 Tạo Tài Khoản</button>
        </form>

        <div class="auth-footer">
            Đã có tài khoản? <a href="<?php echo APP_URL; ?>/auth/login">Đăng nhập ngay!</a>
        </div>
    </div>
</section>
