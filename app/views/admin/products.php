<!-- Admin Products Management Page -->
<section class="admin-section">
    <div class="admin-header">
        <h1>📦 Quản Lý Sản Phẩm</h1>
        <a href="<?php echo APP_URL; ?>/admin/add-product" class="btn-add">+ Thêm Sản Phẩm</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="products-table-container">
        <?php if (empty($products)): ?>
            <div class="empty-message">
                <p>Chưa có sản phẩm nào.</p>
                <a href="<?php echo APP_URL; ?>/admin/add-product" class="btn-add">Thêm sản phẩm đầu tiên</a>
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Giá Gốc</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td>
                                <?php if (isset($product['image_path']) && $product['image_path']): ?>
                                    <img src="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($product['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         class="product-thumb">
                                <?php else: ?>
                                    <div class="no-image">🖼️</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/home/detail/<?php echo $product['product_id']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'Chưa phân loại'); ?></td>
                            <td class="price"><?php echo number_format($product['original_price'], 0, ',', '.'); ?>đ</td>
                            <td class="actions">
                                <a href="<?php echo APP_URL; ?>/admin/edit-product/<?php echo $product['product_id']; ?>" class="btn-edit" title="Sửa">✏️</a>
                                <a href="<?php echo APP_URL; ?>/admin/delete-product/<?php echo $product['product_id']; ?>" 
                                   class="btn-delete" 
                                   title="Xóa"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<style>
.admin-section {
    padding: 2rem;
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

.btn-add {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    background: linear-gradient(135deg, #ff6b9d, #c44569);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: transform 0.3s, box-shadow 0.3s;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 157, 0.4);
}

.products-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.admin-table tbody tr:hover {
    background: #fafafa;
}

.product-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.no-image {
    width: 60px;
    height: 60px;
    background: #f0f0f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.admin-table td a {
    color: #ff6b9d;
    text-decoration: none;
    font-weight: 500;
}

.admin-table td a:hover {
    text-decoration: underline;
}

.price {
    font-weight: bold;
    color: #ff6b9d;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-edit,
.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 1.1rem;
    transition: transform 0.2s;
}

.btn-edit {
    background: #e3f2fd;
}

.btn-delete {
    background: #ffebee;
}

.btn-edit:hover,
.btn-delete:hover {
    transform: scale(1.1);
}

.empty-message {
    text-align: center;
    padding: 3rem;
}

.empty-message p {
    color: #666;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
