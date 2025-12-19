<!-- Add Product Page -->
<section class="add-product-section">
    <div class="add-product-container">
        <div class="page-header">
            <a href="<?php echo APP_URL; ?>/admin/products" class="back-btn">← Quay lại</a>
            <h1>🛍️ Thêm Sản Phẩm Mới</h1>
            <p class="subtitle">Điền thông tin sản phẩm bên dưới</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <div class="alert-content">
                    <strong>Có lỗi xảy ra:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <div class="form-grid">
                <!-- Left Column -->
                <div class="form-column">
                    <div class="form-card">
                        <h3>📝 Thông Tin Cơ Bản</h3>
                        
                        <div class="form-group">
                            <label for="name">
                                <span class="label-icon">🏷️</span>
                                Tên Sản Phẩm <span class="required">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   placeholder="VD: Áo thun unisex basic"
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category_id">
                                <span class="label-icon">📂</span>
                                Danh Mục <span class="required">*</span>
                            </label>
                            <select id="category_id" name="category_id" required>
                                <option value="">-- Chọn Danh Mục --</option>
                                <option value="1" <?php echo ($categoryId ?? 0) == 1 ? 'selected' : ''; ?>>👕 Áo</option>
                                <option value="2" <?php echo ($categoryId ?? 0) == 2 ? 'selected' : ''; ?>>👖 Quần</option>
                                <option value="3" <?php echo ($categoryId ?? 0) == 3 ? 'selected' : ''; ?>>🎒 Phụ Kiện</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="original_price">
                                <span class="label-icon">💰</span>
                                Giá Gốc (VNĐ) <span class="required">*</span>
                            </label>
                            <div class="input-with-suffix">
                                <input type="number" id="original_price" name="original_price" 
                                       step="1000" min="0" placeholder="VD: 350000"
                                       value="<?php echo htmlspecialchars($originalPrice ?? ''); ?>" required>
                                <span class="suffix">VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="form-column">
                    <div class="form-card">
                        <h3>📷 Hình Ảnh</h3>
                        
                        <div class="form-group">
                            <label for="image">
                                <span class="label-icon">🖼️</span>
                                Ảnh Đại Diện
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <span class="upload-icon">📤</span>
                                    <p>Kéo thả ảnh vào đây hoặc</p>
                                    <span class="upload-btn">Chọn Ảnh</span>
                                </div>
                                <img id="imagePreview" class="image-preview" style="display: none;">
                            </div>
                            <input type="file" id="image" name="image" accept="image/*" hidden>
                            <small class="file-info">📌 Định dạng: JPG, PNG, GIF, WEBP | Tối đa: 5MB</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Width Description -->
            <div class="form-card full-width">
                <h3>📄 Mô Tả Sản Phẩm</h3>
                
                <div class="form-group">
                    <label for="description">
                        <span class="label-icon">✏️</span>
                        Chi Tiết Sản Phẩm
                    </label>
                    <textarea id="description" name="description" rows="5" 
                              placeholder="Nhập mô tả chi tiết về sản phẩm: chất liệu, kích thước, hướng dẫn bảo quản..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <a href="<?php echo APP_URL; ?>/admin/products" class="btn-cancel">Hủy Bỏ</a>
                <button type="submit" class="btn-submit">
                    <span class="btn-icon">✓</span>
                    Thêm Sản Phẩm
                </button>
            </div>
        </form>
    </div>
</section>

<style>
.add-product-section {
    padding: 30px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    min-height: calc(100vh - 80px);
}

.add-product-container {
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.back-btn {
    display: inline-block;
    margin-bottom: 15px;
    color: #666;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.back-btn:hover {
    color: #ff6b9d;
}

.page-header h1 {
    margin: 0 0 10px;
    font-size: 32px;
    color: #333;
}

.page-header .subtitle {
    color: #888;
    margin: 0;
}

/* Alert Styles */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.alert-error {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    border: 1px solid #fc8181;
}

.alert-icon {
    font-size: 24px;
}

.alert-content strong {
    display: block;
    margin-bottom: 8px;
    color: #c53030;
}

.alert-content ul {
    margin: 0;
    padding-left: 20px;
    color: #9b2c2c;
}

.alert-content li {
    margin-bottom: 5px;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

/* Form Card */
.form-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.form-card.full-width {
    margin-bottom: 25px;
}

.form-card h3 {
    margin: 0 0 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    color: #333;
    font-size: 18px;
}

/* Form Group */
.form-group {
    margin-bottom: 20px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-weight: 600;
    color: #444;
}

.label-icon {
    font-size: 16px;
}

.required {
    color: #e53e3e;
}

/* Input Styles */
.form-group input[type="text"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    font-family: inherit;
    transition: all 0.3s ease;
    background: #fafafa;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff6b9d;
    background: white;
    box-shadow: 0 0 0 4px rgba(255, 107, 157, 0.1);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #a0aec0;
}

.form-group select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 40px;
}

/* Input with suffix */
.input-with-suffix {
    position: relative;
}

.input-with-suffix input {
    padding-right: 60px;
}

.input-with-suffix .suffix {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-weight: 500;
}

/* Upload Area */
.upload-area {
    border: 2px dashed #cbd5e0;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafafa;
}

.upload-area:hover,
.upload-area.dragover {
    border-color: #ff6b9d;
    background: #fff5f8;
}

.upload-placeholder {
    color: #666;
}

.upload-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 10px;
}

.upload-placeholder p {
    margin: 0 0 10px;
    color: #888;
}

.upload-btn {
    display: inline-block;
    padding: 8px 20px;
    background: #ff6b9d;
    color: white;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
}

.image-preview {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    object-fit: contain;
}

.file-info {
    display: block;
    margin-top: 10px;
    color: #888;
    font-size: 13px;
}

/* Textarea */
.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.btn-cancel {
    padding: 14px 35px;
    background: #e2e8f0;
    color: #4a5568;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #cbd5e0;
}

.btn-submit {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 40px;
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 157, 0.5);
}

.btn-icon {
    font-size: 18px;
}
</style>

<script>
// Image Upload Preview
const uploadArea = document.getElementById('uploadArea');
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');

uploadArea.addEventListener('click', () => imageInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        imageInput.files = e.dataTransfer.files;
        showPreview(e.dataTransfer.files[0]);
    }
});

imageInput.addEventListener('change', (e) => {
    if (e.target.files.length) {
        showPreview(e.target.files[0]);
    }
});

function showPreview(file) {
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            uploadPlaceholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Format price input
const priceInput = document.getElementById('original_price');
priceInput.addEventListener('input', function() {
    // Remove non-numeric characters except for the value
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

