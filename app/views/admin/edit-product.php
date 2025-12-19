<!-- Admin Edit Product Page -->
<section class="admin-section">
    <div class="admin-header">
        <h1>✏️ Chỉnh Sửa Sản Phẩm</h1>
        <a href="<?php echo APP_URL; ?>/admin/products" class="btn-back">← Quay Lại</a>
    </div>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="name">Tên Sản Phẩm: <span class="required">*</span></label>
                <input type="text" id="name" name="name" required
                       value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục: <span class="required">*</span></label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Chọn Danh Mục --</option>
                    <option value="1" <?php echo (isset($product['category_id']) && $product['category_id'] == 1) ? 'selected' : ''; ?>>Áo</option>
                    <option value="2" <?php echo (isset($product['category_id']) && $product['category_id'] == 2) ? 'selected' : ''; ?>>Quần</option>
                    <option value="3" <?php echo (isset($product['category_id']) && $product['category_id'] == 3) ? 'selected' : ''; ?>>Phụ Kiện</option>
                </select>
            </div>

            <div class="form-group">
                <label for="original_price">Giá Gốc (VNĐ): <span class="required">*</span></label>
                <input type="number" id="original_price" name="original_price" min="0" step="1000" required
                       value="<?php echo htmlspecialchars($product['original_price'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="description">Mô Tả:</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Hình Ảnh:</label>
                <?php if (isset($product['image_path']) && $product['image_path']): ?>
                    <div class="current-image">
                        <p>Ảnh hiện tại:</p>
                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($product['image_path']); ?>" 
                             alt="Current Image" class="preview-image">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
                <small class="form-hint">Để trống nếu không muốn thay đổi ảnh. Định dạng: JPG, PNG, GIF, WEBP. Tối đa 5MB.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">💾 Lưu Thay Đổi</button>
                <a href="<?php echo APP_URL; ?>/admin/products" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</section>

<style>
.admin-section {
    padding: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.admin-header h1 {
    margin: 0;
    color: #333;
}

.btn-back {
    display: inline-block;
    padding: 0.6rem 1.2rem;
    background: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.3s;
}

.btn-back:hover {
    background: #ddd;
}

.form-container {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.product-form .form-group {
    margin-bottom: 1.5rem;
}

.product-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

.required {
    color: #ff6b9d;
}

.product-form input[type="text"],
.product-form input[type="number"],
.product-form select,
.product-form textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.product-form input:focus,
.product-form select:focus,
.product-form textarea:focus {
    outline: none;
    border-color: #ff6b9d;
}

.product-form input[type="file"] {
    padding: 0.5rem 0;
}

.form-hint {
    display: block;
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.85rem;
}

.current-image {
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.current-image p {
    margin: 0 0 0.5rem 0;
    color: #666;
    font-size: 0.9rem;
}

.preview-image {
    max-width: 200px;
    max-height: 200px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-submit {
    flex: 1;
    padding: 1rem;
    background: linear-gradient(135deg, #ff6b9d, #c44569);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 157, 0.4);
}

.btn-cancel {
    flex: 0.5;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
}

.btn-cancel:hover {
    background: #ddd;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-error ul {
    margin: 0;
    padding-left: 1.2rem;
}

.alert-error li {
    margin-bottom: 0.3rem;
}
</style>
