<!-- Edit Promotion Page -->
<section class="admin-section">
    <div class="admin-sidebar">
        <nav class="admin-nav">
            <ul>
                <li><a href="<?php echo APP_URL; ?>/admin">Dashboard</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/products">Quản Lý Sản Phẩm</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/add-product">Thêm Sản Phẩm</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/promotions" class="active">Mã Khuyến Mãi</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/orders">Quản Lý Đơn Hàng</a></li>
                <li><a href="<?php echo APP_URL; ?>/auth/logout">Đăng Xuất</a></li>
            </ul>
        </nav>
    </div>

    <div class="admin-content">
        <h1>Chỉnh Sửa Mã Khuyến Mãi</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="promotion-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="code">Mã Khuyến Mãi:</label>
                    <input type="text" id="code" name="code" 
                           value="<?php echo htmlspecialchars($promotion['code'] ?? ''); ?>" 
                           placeholder="VD: SUMMER20, SAVE10" required>
                    <small>Chỉ hỗ trợ chữ cái, số và dấu gạch ngang</small>
                </div>

                <div class="form-group">
                    <label for="discount_type">Loại Giảm:</label>
                    <select id="discount_type" name="discount_type" required onchange="updateValueLabel()">
                        <option value="">-- Chọn Loại --</option>
                        <option value="percentage" <?php echo ($promotion['discount_type'] ?? '') === 'percentage' ? 'selected' : ''; ?>>
                            Phần Trăm (%)
                        </option>
                        <option value="fixed" <?php echo ($promotion['discount_type'] ?? '') === 'fixed' ? 'selected' : ''; ?>>
                            Tiền Cố Định (đ)
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="discount_value">Giá Trị Giảm:</label>
                    <div class="input-with-unit">
                        <input type="number" id="discount_value" name="discount_value" 
                               value="<?php echo htmlspecialchars($promotion['discount_value'] ?? ''); ?>" 
                               step="0.01" min="0" placeholder="0" required>
                        <span id="valueUnit">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="start_date">Ngày Bắt Đầu:</label>
                    <input type="datetime-local" id="start_date" name="start_date" 
                           value="<?php echo htmlspecialchars(str_replace(' ', 'T', $promotion['start_date'] ?? '')); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="end_date">Ngày Kết Thúc:</label>
                    <input type="datetime-local" id="end_date" name="end_date" 
                           value="<?php echo htmlspecialchars(str_replace(' ', 'T', $promotion['end_date'] ?? '')); ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Cập Nhật Mã Khuyến Mãi</button>
                <a href="<?php echo APP_URL; ?>/admin/promotions" class="btn-cancel">Hủy</a>
            </div>
        </form>

        <!-- Info Box -->
        <div class="info-box">
            <h3>ℹ️ Thông Tin</h3>
            <p>Chỉnh sửa thông tin mã khuyến mãi. Những thay đổi sẽ có hiệu lực ngay lập tức cho các khách hàng mới áp dụng mã.</p>
        </div>
    </div>
</section>

<script>
function updateValueLabel() {
    const discountType = document.getElementById('discount_type').value;
    const valueUnit = document.getElementById('valueUnit');
    
    if (discountType === 'percentage') {
        valueUnit.textContent = '%';
    } else if (discountType === 'fixed') {
        valueUnit.textContent = 'đ';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateValueLabel();
});
</script>

<style>
.promotion-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.form-group input,
.form-group select {
    padding: 0.7rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #ff6b9d;
    box-shadow: 0 0 5px rgba(255, 107, 157, 0.3);
}

.form-group small {
    margin-top: 0.3rem;
    color: #999;
    font-size: 0.85rem;
}

.input-with-unit {
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-unit input {
    flex: 1;
}

.input-with-unit span {
    position: absolute;
    right: 1rem;
    font-weight: bold;
    color: #666;
    pointer-events: none;
}

.form-actions {
    display: flex;
    gap: 1rem;
}

.btn-submit,
.btn-cancel {
    flex: 1;
    padding: 0.8rem;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    font-size: 1rem;
    transition: all 0.3s;
}

.btn-submit {
    background: #ff6b9d;
    color: white;
}

.btn-submit:hover {
    background: #c44569;
}

.btn-cancel {
    background: #f0f0f0;
    color: #333;
}

.btn-cancel:hover {
    background: #e0e0e0;
}

.info-box {
    background: #e7f3ff;
    border-left: 4px solid #007bff;
    padding: 1.5rem;
    border-radius: 4px;
}

.info-box h3 {
    margin-bottom: 1rem;
    color: #007bff;
}

.info-box p {
    line-height: 1.6;
}

.alert {
    padding: 0.8rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-error ul {
    margin-left: 1.5rem;
}

.alert-error li {
    margin-bottom: 0.3rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
