<?php
/**
 * Report Controller
 * API Báo cáo & Thống kê
 */

class ReportController extends BaseController {

    public function __construct($db) {
        parent::__construct($db);
    }

    public function processRequest() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriParts = explode('/', trim($uri, '/'));

        // Remove 'api' and 'reports'
        array_shift($uriParts);
        array_shift($uriParts);

        $action = $uriParts[0] ?? null;

        if ($this->requestMethod !== 'GET') {
            $this->methodNotAllowed();
        }

        switch ($action) {
            case 'revenue':
                $this->revenueReport();
                break;
            case 'customers':
                $this->customerReport();
                break;
            case 'products':
                $this->productReport();
                break;
            default:
                Response::notFound('Endpoint not found for reports');
        }
    }

    /**
     * GET /api/reports/revenue?group_by=daily|monthly|yearly&start=YYYY-MM-DD&end=YYYY-MM-DD
     */
    private function revenueReport() {
        $groupBy = $_GET['group_by'] ?? 'daily';
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        // Defaults: last 30 days for daily, 12 months for monthly, 3 years for yearly
        if (!$start || !$end) {
            $now = new DateTime('now');
            if ($groupBy === 'monthly') {
                $startDate = (clone $now)->modify('-11 months')->modify('first day of this month')->setTime(0,0,0);
                $endDate = (clone $now)->setTime(23,59,59);
            } else if ($groupBy === 'yearly') {
                $startDate = (clone $now)->modify('-2 years')->modify('first day of January')->setTime(0,0,0);
                $endDate = (clone $now)->setTime(23,59,59);
            } else { // daily
                $startDate = (clone $now)->modify('-29 days')->setTime(0,0,0);
                $endDate = (clone $now)->setTime(23,59,59);
            }
            $start = $startDate->format('Y-m-d H:i:s');
            $end = $endDate->format('Y-m-d H:i:s');
        } else {
            $start .= (strlen($start) === 10 ? ' 00:00:00' : '');
            $end .= (strlen($end) === 10 ? ' 23:59:59' : '');
        }

        // Build date grouping
        switch ($groupBy) {
            case 'monthly':
                $periodExpr = "DATE_FORMAT(payment_date, '%Y-%m')";
                $orderExpr = "DATE_FORMAT(payment_date, '%Y-%m')";
                break;
            case 'yearly':
                $periodExpr = "DATE_FORMAT(payment_date, '%Y')";
                $orderExpr = "DATE_FORMAT(payment_date, '%Y')";
                break;
            default:
                $periodExpr = "DATE(payment_date)";
                $orderExpr = "DATE(payment_date)";
        }

        try {
            // Use payments as source of recognized revenue
            $sql = "SELECT $periodExpr AS period, 
                           SUM(amount) AS total_revenue, 
                           COUNT(*) AS transactions
                    FROM payments
                    WHERE payment_date BETWEEN :start AND :end
                    GROUP BY period
                    ORDER BY $orderExpr ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':start' => $start, ':end' => $end]);
            $rows = $stmt->fetchAll();

            Response::success([
                'group_by' => $groupBy,
                'start' => $start,
                'end' => $end,
                'rows' => $rows
            ], 'Báo cáo doanh thu');
        } catch (Exception $e) {
            Response::serverError('Lỗi báo cáo doanh thu: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/reports/customers?metric=summary|top_spenders&start=YYYY-MM-DD&end=YYYY-MM-DD&limit=10
     */
    private function customerReport() {
        $metric = $_GET['metric'] ?? 'summary';
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (!$start || !$end) {
            $now = new DateTime('now');
            $start = (clone $now)->modify('-30 days')->format('Y-m-d 00:00:00');
            $end = $now->format('Y-m-d 23:59:59');
        } else {
            $start .= (strlen($start) === 10 ? ' 00:00:00' : '');
            $end .= (strlen($end) === 10 ? ' 23:59:59' : '');
        }

        try {
            if ($metric === 'top_spenders') {
                $sql = "SELECT c.customer_id, c.full_name, c.email, c.phone_number,
                               SUM(p.amount) AS total_spent, COUNT(p.payment_id) AS payments
                        FROM customers c
                        JOIN orders o ON o.customer_id = c.customer_id
                        JOIN payments p ON p.order_id = o.order_id
                        WHERE p.payment_date BETWEEN :start AND :end
                        GROUP BY c.customer_id, c.full_name, c.email, c.phone_number
                        ORDER BY total_spent DESC
                        LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':start', $start);
                $stmt->bindValue(':end', $end);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();

                Response::success([
                    'metric' => $metric,
                    'start' => $start,
                    'end' => $end,
                    'rows' => $rows
                ], 'Top khách hàng chi tiêu nhiều nhất');
                return;
            }

            // Default summary metric
            $summary = [];

            // New customers in range
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM customers WHERE created_at BETWEEN :start AND :end");
            $stmt->execute([':start' => $start, ':end' => $end]);
            $summary['new_customers'] = (int)$stmt->fetch()['total'];

            // Customers with orders in range
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT o.customer_id) AS total
                                         FROM orders o
                                         WHERE o.order_date BETWEEN :start AND :end");
            $stmt->execute([':start' => $start, ':end' => $end]);
            $summary['active_customers'] = (int)$stmt->fetch()['total'];

            // Total revenue by customers (payments)
            $stmt = $this->db->prepare("SELECT SUM(p.amount) AS total
                                         FROM payments p
                                         WHERE p.payment_date BETWEEN :start AND :end");
            $stmt->execute([':start' => $start, ':end' => $end]);
            $summary['revenue'] = (float)($stmt->fetch()['total'] ?? 0);

            Response::success([
                'metric' => 'summary',
                'start' => $start,
                'end' => $end,
                'summary' => $summary
            ], 'Tổng quan khách hàng');
        } catch (Exception $e) {
            Response::serverError('Lỗi báo cáo khách hàng: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/reports/products?metric=best_selling|low_stock|most_stock&start=YYYY-MM-DD&end=YYYY-MM-DD&limit=10
     */
    private function productReport() {
        $metric = $_GET['metric'] ?? 'best_selling';
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (!$start || !$end) {
            $now = new DateTime('now');
            $start = (clone $now)->modify('-30 days')->format('Y-m-d 00:00:00');
            $end = $now->format('Y-m-d 23:59:59');
        } else {
            $start .= (strlen($start) === 10 ? ' 00:00:00' : '');
            $end .= (strlen($end) === 10 ? ' 23:59:59' : '');
        }

        try {
            if ($metric === 'low_stock') {
                $sql = "SELECT p.product_id, p.name, pv.variant_id, pv.size, pv.color, COALESCE(i.quantity, 0) AS quantity
                        FROM productvariants pv
                        JOIN products p ON p.product_id = pv.product_id
                        LEFT JOIN inventory i ON i.variant_id = pv.variant_id
                        ORDER BY COALESCE(i.quantity, 0) ASC
                        LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();

                Response::success([
                    'metric' => $metric,
                    'rows' => $rows
                ], 'Danh sách biến thể tồn kho thấp');
                return;
            }

            if ($metric === 'most_stock') {
                $sql = "SELECT p.product_id, p.name, pv.variant_id, pv.size, pv.color, COALESCE(i.quantity, 0) AS quantity
                        FROM productvariants pv
                        JOIN products p ON p.product_id = pv.product_id
                        LEFT JOIN inventory i ON i.variant_id = pv.variant_id
                        ORDER BY COALESCE(i.quantity, 0) DESC
                        LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();

                Response::success([
                    'metric' => $metric,
                    'rows' => $rows
                ], 'Danh sách biến thể tồn kho cao');
                return;
            }

            // Default: best_selling (by revenue within date range, only for orders likely paid/confirmed)
            $sql = "SELECT p.product_id, p.name,
                           SUM(od.quantity) AS total_quantity,
                           SUM(od.quantity * od.unit_price - od.discount_amount) AS total_revenue
                    FROM orderdetails od
                    JOIN orders o ON o.order_id = od.order_id
                    JOIN productvariants pv ON pv.variant_id = od.variant_id
                    JOIN products p ON p.product_id = pv.product_id
                    WHERE o.order_date BETWEEN :start AND :end
                      AND o.status IN ('confirmed','processing','shipping','completed')
                    GROUP BY p.product_id, p.name
                    ORDER BY total_revenue DESC
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':start', $start);
            $stmt->bindValue(':end', $end);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            Response::success([
                'metric' => 'best_selling',
                'start' => $start,
                'end' => $end,
                'rows' => $rows
            ], 'Sản phẩm bán chạy');
        } catch (Exception $e) {
            Response::serverError('Lỗi báo cáo sản phẩm: ' . $e->getMessage());
        }
    }
}
?>
