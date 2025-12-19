<!-- Cart Page -->
<section class="cart-section">
    <div class="cart-items">
        <h2>🛒 Giỏ Hàng Của Bạn</h2>
        
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <?php 
                            $imageSrc = !empty($item['variant']['image_path'])
                                ? APP_URL . '/uploads/' . $item['variant']['image_path']
                                : APP_URL . '/images/no-image.png';
                        ?>
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($item['variant']['product_name'] ?? 'Product'); ?>">
                    </div>
                    <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($item['variant']['product_name'] ?? 'Sản phẩm'); ?></h3>
                        <p>📏 Size: <?php echo htmlspecialchars($item['variant']['size'] ?? 'N/A'); ?></p>
                        <p>🎨 Màu: <?php echo htmlspecialchars($item['variant']['color'] ?? 'N/A'); ?></p>
                        <p class="cart-item-price">
                            <?php echo number_format($item['variant']['current_price'] ?? 0, 0, ',', '.'); ?>đ
                        </p>
                    </div>
                    <div class="cart-item-quantity">
                        <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="10" 
                               onchange="updateCartItem(<?php echo $item['variant']['variant_id']; ?>, this.value)">
                    </div>
                    <div class="cart-item-total">
                        <strong><?php echo number_format($item['itemTotal'] ?? 0, 0, ',', '.'); ?>đ</strong>
                    </div>
                    <button class="cart-item-remove" 
                            onclick="removeFromCart(<?php echo $item['variant']['variant_id']; ?>)">
                        🗑️ Xoá
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <h3>Giỏ hàng trống!</h3>
                <p>Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="<?php echo APP_URL; ?>" class="btn-continue-shopping">
                    🛍️ Tiếp tục mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="cart-summary">
        <h2>📋 Tóm Tắt Đơn Hàng</h2>
        
        <?php if (!empty($cartItems)): ?>
            <div class="promotion-code">
                <label>🎁 Mã Khuyến Mãi</label>
                <input type="text" id="promotionCode" placeholder="Nhập mã giảm giá...">
                <button onclick="applyPromotion()">Áp Dụng</button>
            </div>

            <div class="summary-row">
                <span>Tạm Tính:</span>
                <span><?php echo number_format($subtotal ?? 0, 0, ',', '.'); ?>đ</span>
            </div>

            <?php if (isset($discount) && $discount > 0): ?>
                <div class="summary-row" style="color: var(--success-dark);">
                    <span>🎉 Giảm Giá:</span>
                    <span>-<?php echo number_format($discount, 0, ',', '.'); ?>đ</span>
                </div>
            <?php endif; ?>

            <div class="summary-row">
                <span><strong>Tổng Cộng:</strong></span>
                <span><strong><?php echo number_format($total ?? 0, 0, ',', '.'); ?>đ</strong></span>
            </div>

            <button class="btn-checkout" onclick="checkout()">
                💳 Thanh Toán Ngay
            </button>
        <?php else: ?>
            <p style="text-align: center; padding: 30px; color: var(--text-medium);">
                Thêm sản phẩm vào giỏ để xem tóm tắt
            </p>
            <a href="<?php echo APP_URL; ?>" class="btn-checkout" style="text-decoration: none;">
                🏠 Về Trang Chủ
            </a>
        <?php endif; ?>
    </div>
</section>

<script>
function updateCartItem(variantId, quantity) {
    fetch('<?php echo APP_URL; ?>/cart/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ variant_id: variantId, quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + data.error);
        }
    });
}

function removeFromCart(variantId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    
    fetch('<?php echo APP_URL; ?>/cart/remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ variant_id: variantId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + data.error);
        }
    });
}

function applyPromotion() {
    const code = document.getElementById('promotionCode').value.trim();
    if (!code) {
        alert('Vui lòng nhập mã khuyến mãi');
        return;
    }
    
    fetch('<?php echo APP_URL; ?>/cart/apply-promotion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Áp dụng mã thành công!');
            location.reload();
        } else {
            alert('Lỗi: ' + data.error);
        }
    });
}

function checkout() {
    alert('Chức năng thanh toán đang được phát triển! 🚀');
}
</script>
