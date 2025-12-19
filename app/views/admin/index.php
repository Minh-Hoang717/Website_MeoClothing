<!-- Admin Dashboard -->
<section class="admin-section">
    <div class="admin-sidebar">
        <nav class="admin-nav">
            <ul>
                <li><a href="<?php echo APP_URL; ?>/admin" class="active">Dashboard</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/products">Quản Lý Sản Phẩm</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/add-product">Thêm Sản Phẩm</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/promotions">Mã Khuyến Mãi</a></li>
                <li><a href="<?php echo APP_URL; ?>/admin/orders">Quản Lý Đơn Hàng</a></li>
                <li><a href="<?php echo APP_URL; ?>/auth/logout">Đăng Xuất</a></li>
            </ul>
        </nav>
    </div>

    <div class="admin-content">
        <h1>📊 Dashboard</h1>
        
        <!-- Key Metrics -->
        <div class="stats-grid">
            <div class="stat-card stat-blue">
                <h3>🛍️ Tổng Sản Phẩm</h3>
                <p class="stat-value"><?php echo number_format($totalProducts); ?></p>
            </div>

            <div class="stat-card stat-green">
                <h3>📦 Tổng Đơn Hàng</h3>
                <p class="stat-value"><?php echo number_format($totalOrders); ?></p>
            </div>

            <div class="stat-card stat-purple">
                <h3>👥 Tổng Người Dùng</h3>
                <p class="stat-value"><?php echo number_format($totalUsers); ?></p>
            </div>

            <div class="stat-card stat-orange">
                <h3>💰 Tổng Doanh Thu</h3>
                <p class="stat-value revenue">
                    <?php echo number_format($totalRevenue, 0, ',', '.'); ?>đ
                </p>
            </div>
        </div>

        <!-- Top 5 Best-Selling Products -->
        <div class="report-section">
            <h2>🔥 Top 5 Sản Phẩm Bán Chạy Nhất</h2>
            
            <?php if (!empty($topProducts)): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Số Lượng Bán</th>
                            <th>Số Đơn Hàng</th>
                            <th>Giá Trung Bình</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $index => $product): ?>
                            <tr>
                                <td>
                                    <span class="rank-badge rank-<?php echo $index + 1; ?>">
                                        <?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                </td>
                                <td>
                                    <span class="quantity-badge">
                                        <?php echo number_format($product['total_sold']); ?> cái
                                    </span>
                                </td>
                                <td><?php echo number_format($product['order_count']); ?> đơn</td>
                                <td><?php echo number_format($product['avg_price'], 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Chưa có dữ liệu bán hàng</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <h3>🔗 Truy Cập Nhanh</h3>
            <div class="links-grid">
                <a href="<?php echo APP_URL; ?>/admin/products" class="quick-link">
                    <span>📋</span> Quản Lý Sản Phẩm
                </a>
                <a href="<?php echo APP_URL; ?>/admin/add-product" class="quick-link">
                    <span>➕</span> Thêm Sản Phẩm
                </a>
                <a href="<?php echo APP_URL; ?>/admin/promotions" class="quick-link">
                    <span>🎁</span> Mã Khuyến Mãi
                </a>
                <a href="<?php echo APP_URL; ?>/admin/orders" class="quick-link">
                    <span>📦</span> Đơn Hàng
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.admin-section {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    margin: 2rem 0;
}

.admin-sidebar {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    height: fit-content;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 20px;
}

.admin-nav ul {
    list-style: none;
}

.admin-nav li {
    margin-bottom: 0.5rem;
}

.admin-nav a {
    display: block;
    padding: 0.8rem;
    color: #333;
    border-radius: 4px;
    transition: all 0.3s;
}

.admin-nav a:hover,
.admin-nav a.active {
    background: #ff6b9d;
    color: white;
}

.admin-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.admin-content h1 {
    margin-bottom: 2rem;
    font-size: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    color: white;
    padding: 2rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card h3 {
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
}

.stat-value.revenue {
    font-size: 1.8rem;
}

.stat-blue {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-green {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-purple {
    background: linear-gradient(135deg, #9c27b0 0%, #e91e63 100%);
}

.stat-orange {
    background: linear-gradient(135deg, #ff9800 0%, #f44336 100%);
}

/* Report Section */
.report-section {
    background: #f9f9f9;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.report-section h2 {
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.report-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
}

.report-table th,
.report-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.report-table tbody tr:hover {
    background: #f5f5f5;
}

.report-table tbody tr:last-child td {
    border-bottom: none;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-weight: bold;
    color: white;
    font-size: 0.9rem;
}

.rank-1 {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
}

.rank-2 {
    background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%);
}

.rank-3 {
    background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
}

.rank-4 {
    background: #667eea;
}

.rank-5 {
    background: #764ba2;
}

.quantity-badge {
    display: inline-block;
    background: #e7f3ff;
    color: #007bff;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #999;
    background: white;
    border-radius: 8px;
}

/* Quick Links */
.quick-links {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 8px;
}

.quick-links h3 {
    margin-bottom: 1.5rem;
}

.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.quick-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.quick-link:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: white;
    transform: translateY(-3px);
}

.quick-link span {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .admin-section {
        grid-template-columns: 1fr;
    }

    .admin-sidebar {
        display: none;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .stat-value {
        font-size: 1.8rem;
    }

    .report-table {
        font-size: 0.9rem;
    }

    .report-table th,
    .report-table td {
        padding: 0.7rem;
    }
}
</style>
