<!-- Hero Section -->
<section class="hero-section">
    <h2>🐱 Chào Mừng Đến Meow Clothing!</h2>
    <p>Khám phá bộ sưu tập thời trang dễ thương, chất lượng cao với giá cả phải chăng nhất!</p>
</section>

<!-- Home Page - Product List -->
<section class="products-section">
    <h2>✨ Sản Phẩm Nổi Bật</h2>
    
    <div class="products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php 
                            $imagePath = isset($product['image_path']) ? $product['image_path'] : null;
                            $imageSrc = $imagePath 
                                ? APP_URL . '/uploads/' . $imagePath
                                : APP_URL . '/images/no-image.png';
                        ?>
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php if ($product['in_stock']): ?>
                            <span class="in-stock-badge">Còn hàng</span>
                        <?php else: ?>
                            <span class="out-of-stock">Hết hàng</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3>
                            <a href="<?php echo APP_URL; ?>/home/detail/<?php echo $product['product_id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <p class="category">📁 <?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="price">
                            <?php echo number_format($product['original_price'], 0, ',', '.'); ?>đ
                        </p>
                        <div class="product-actions">
                            <a href="<?php echo APP_URL; ?>/home/detail/<?php echo $product['product_id']; ?>" class="btn-view">
                                👁️ Chi Tiết
                            </a>
                            <?php if ($product['in_stock']): ?>
                                <button class="btn-add-cart" onclick="addToCart(event, <?php echo $product['product_id']; ?>)">
                                    🛒 Thêm
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <div class="no-products-icon">🐱</div>
                <h3>Chưa có sản phẩm nào</h3>
                <p>Hãy quay lại sau nhé!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="<?php echo APP_URL; ?>?page=<?php echo $currentPage - 1; ?>" class="page-link">← Trước</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?php echo APP_URL; ?>?page=<?php echo $i; ?>" 
                   class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?php echo APP_URL; ?>?page=<?php echo $currentPage + 1; ?>" class="page-link">Sau →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
