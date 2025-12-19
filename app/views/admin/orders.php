<!-- Admin Orders Management Page -->
<section class="admin-section">
    <div class="admin-header">
        <h1>📋 Quản Lý Đơn Hàng</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="orders-table-container">
        <?php if (empty($orders)): ?>
            <div class="empty-message">
                <p>Chưa có đơn hàng nào.</p>
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td>
                                <?php echo isset($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'User #' . $order['user_id']; ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                            <td class="price"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php 
                                    $statusLabels = [
                                        'pending' => '⏳ Chờ xử lý',
                                        'processing' => '🔄 Đang xử lý',
                                        'shipped' => '🚚 Đang giao',
                                        'delivered' => '✅ Đã giao',
                                        'completed' => '✅ Hoàn thành',
                                        'cancelled' => '❌ Đã hủy'
                                    ];
                                    echo $statusLabels[$order['status']] ?? $order['status'];
                                    ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn-view" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)" title="Xem chi tiết">
                                    👁️
                                </button>
                                <select class="status-select" onchange="updateOrderStatus(<?php echo $order['order_id']; ?>, this.value)">
                                    <option value="">-- Cập nhật --</option>
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'disabled' : ''; ?>>Chờ xử lý</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'disabled' : ''; ?>>Đang xử lý</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'disabled' : ''; ?>>Đang giao</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'disabled' : ''; ?>>Đã giao</option>
                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'disabled' : ''; ?>>Hoàn thành</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'disabled' : ''; ?>>Hủy đơn</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<!-- Order Details Modal -->
<div id="orderModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeOrderModal()">&times;</span>
        <h2>Chi Tiết Đơn Hàng #<span id="modalOrderId"></span></h2>
        <div id="orderDetailsContent">
            <p>Đang tải...</p>
        </div>
    </div>
</div>

<style>
.admin-section {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.admin-header h1 {
    margin: 0;
    color: #333;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
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

.orders-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.admin-table tr:hover {
    background: #f8f9fa;
}

.price {
    font-weight: 600;
    color: #e74c3c;
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #cce5ff;
    color: #004085;
}

.status-shipped {
    background: #d1ecf1;
    color: #0c5460;
}

.status-delivered,
.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn-view {
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}

.btn-view:hover {
    background: #2980b9;
}

.status-select {
    padding: 6px 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
}

.empty-message {
    padding: 60px 20px;
    text-align: center;
    color: #666;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    padding: 30px;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
}

.close-modal {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 28px;
    font-weight: bold;
    color: #999;
    cursor: pointer;
}

.close-modal:hover {
    color: #333;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.order-total {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 2px solid #333;
    font-size: 18px;
    font-weight: bold;
    text-align: right;
}
</style>

<script>
function updateOrderStatus(orderId, status) {
    if (!status) return;
    
    if (!confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng?')) {
        event.target.value = '';
        return;
    }
    
    fetch('<?php echo APP_URL; ?>/admin/update-order-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cập nhật trạng thái thành công!');
            location.reload();
        } else {
            alert('Lỗi: ' + (data.error || 'Không thể cập nhật'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi cập nhật');
    });
}

function viewOrderDetails(orderId) {
    document.getElementById('orderModal').style.display = 'flex';
    document.getElementById('modalOrderId').textContent = orderId;
    document.getElementById('orderDetailsContent').innerHTML = '<p>Đang tải...</p>';
    
    fetch('<?php echo APP_URL; ?>/admin/order-details/' + orderId)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('orderDetailsContent').innerHTML = '<p>Lỗi: ' + data.error + '</p>';
            return;
        }
        
        let html = '<div class="order-info">';
        html += '<p><strong>Địa chỉ:</strong> ' + (data.order.shipping_address || 'Không có') + '</p>';
        html += '<p><strong>Ghi chú:</strong> ' + (data.order.notes || 'Không có') + '</p>';
        html += '</div>';
        html += '<h3>Sản phẩm:</h3>';
        
        if (data.details && data.details.length > 0) {
            data.details.forEach(item => {
                html += '<div class="order-item">';
                html += '<span>' + item.product_name + ' (' + item.size + ', ' + item.color + ') x' + item.quantity + '</span>';
                html += '<span>' + formatPrice(item.unit_price * item.quantity) + 'đ</span>';
                html += '</div>';
            });
        }
        
        html += '<div class="order-total">Tổng: ' + formatPrice(data.order.total_amount) + 'đ</div>';
        
        document.getElementById('orderDetailsContent').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('orderDetailsContent').innerHTML = '<p>Đã xảy ra lỗi khi tải dữ liệu</p>';
    });
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
