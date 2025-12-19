<!-- Admin Promotions List -->
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
        <div class="admin-header">
            <h1>Quản Lý Mã Khuyến Mãi</h1>
            <a href="<?php echo APP_URL; ?>/admin/promotions/add" class="btn-add">+ Thêm Mã Mới</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($promotions)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã Khuyến Mãi</th>
                        <th>Loại Giảm</th>
                        <th>Giá Trị</th>
                        <th>Ngày Bắt Đầu</th>
                        <th>Ngày Kết Thúc</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promotions as $promo): ?>
                        <?php 
                            $now = time();
                            $startTime = strtotime($promo['start_date']);
                            $endTime = strtotime($promo['end_date']);
                            
                            if ($now >= $startTime && $now <= $endTime) {
                                $status = 'active';
                                $statusLabel = '✅ Hoạt Động';
                            } elseif ($now < $startTime) {
                                $status = 'pending';
                                $statusLabel = '⏳ Chưa Bắt Đầu';
                            } else {
                                $status = 'expired';
                                $statusLabel = '❌ Hết Hạn';
                            }
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($promo['code']); ?></strong></td>
                            <td>
                                <?php 
                                    if ($promo['discount_type'] === 'percentage') {
                                        echo 'Phần Trăm (%)';
                                    } else {
                                        echo 'Tiền Cố Định (đ)';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if ($promo['discount_type'] === 'percentage') {
                                        echo number_format($promo['discount_value'], 0) . '%';
                                    } else {
                                        echo number_format($promo['discount_value'], 0, ',', '.') . 'đ';
                                    }
                                ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($promo['start_date'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($promo['end_date'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $status; ?>">
                                    <?php echo $statusLabel; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo APP_URL; ?>/admin/promotions/edit/<?php echo $promo['promotion_id']; ?>" 
                                       class="btn-edit">Sửa</a>
                                    <button class="btn-delete" 
                                            onclick="deletePromotion(<?php echo $promo['promotion_id']; ?>)">
                                        Xoá
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>Chưa có mã khuyến mãi nào. <a href="<?php echo APP_URL; ?>/admin/promotions/add">Tạo mới</a></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function deletePromotion(id) {
    if (confirm('Bạn chắc chắn muốn xoá mã khuyến mãi này?')) {
        fetch('/Meow_Clothing_Store/admin/promotions/delete-ajax', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.error || 'Có lỗi xảy ra');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>

<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.btn-add {
    padding: 0.7rem 1.5rem;
    background: #28a745;
    color: white;
    border-radius: 4px;
    font-weight: bold;
    transition: background 0.3s;
}

.btn-add:hover {
    background: #218838;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.admin-table thead {
    background: #f8f9fa;
    font-weight: bold;
    border-bottom: 2px solid #ddd;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.admin-table tbody tr:hover {
    background: #f5f5f5;
}

.status-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-expired {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-edit,
.btn-delete {
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.btn-edit {
    background: #007bff;
    color: white;
}

.btn-edit:hover {
    background: #0056b3;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #999;
    background: white;
    border-radius: 8px;
}

.empty-state a {
    color: #ff6b9d;
    font-weight: bold;
}

.alert {
    padding: 0.8rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
</style>
