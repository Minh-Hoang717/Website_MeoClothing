<?php

namespace models;

use core\Model;

/**
 * Order Model
 */
class Order extends Model
{
    protected $table = 'orders';

    /**
     * Get order with details
     */
    public function getOrderWithDetails($orderId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = :order_id";
        $order = $this->db->prepare($sql)
            ->bind(':order_id', $orderId, \PDO::PARAM_INT)
            ->single();

        if ($order) {
            $order['details'] = $this->getOrderDetails($orderId);
        }

        return $order;
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId)
    {
        $sql = "SELECT od.*, pv.size, pv.color, p.name as product_name
                FROM orderdetails od
                JOIN productvariants pv ON od.variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                WHERE od.order_id = :order_id";

        return $this->db->prepare($sql)
            ->bind(':order_id', $orderId, \PDO::PARAM_INT)
            ->getAll();
    }

    /**
     * Get user orders
     */
    public function getUserOrders($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY order_date DESC";
        return $this->db->prepare($sql)
            ->bind(':user_id', $userId, \PDO::PARAM_INT)
            ->getAll();
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE order_id = :order_id";
        return $this->db->prepare($sql)
            ->bind(':status', $status)
            ->bind(':order_id', $orderId, \PDO::PARAM_INT)
            ->execute();
    }

    /**
     * Get total revenue from completed orders
     */
    public function getTotalRevenue()
    {
        $sql = "SELECT SUM(total_amount) as total_revenue 
                FROM {$this->table} 
                WHERE status = 'completed'";
        
        $result = $this->db->prepare($sql)->single();
        return $result['total_revenue'] ?? 0;
    }

    /**
     * Get top N best-selling products
     */
    public function getTopProducts($limit = 5)
    {
        $sql = "SELECT 
                    p.product_id,
                    p.name as product_name,
                    p.image_path,
                    SUM(od.quantity) as total_sold,
                    COUNT(DISTINCT od.order_id) as order_count,
                    AVG(od.unit_price) as avg_price
                FROM orderdetails od
                JOIN productvariants pv ON od.variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                GROUP BY p.product_id, p.name, p.image_path
                ORDER BY total_sold DESC
                LIMIT :limit";
        
        return $this->db->prepare($sql)
            ->bind(':limit', $limit, \PDO::PARAM_INT)
            ->getAll();
    }

    /**
     * Get monthly revenue
     */
    public function getMonthlyRevenue($months = 12)
    {
        $sql = "SELECT 
                    DATE_FORMAT(order_date, '%Y-%m') as month,
                    SUM(total_amount) as revenue,
                    COUNT(*) as order_count
                FROM {$this->table}
                WHERE status = 'completed' AND order_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                GROUP BY DATE_FORMAT(order_date, '%Y-%m')
                ORDER BY month DESC";
        
        return $this->db->prepare($sql)
            ->bind(':months', $months, \PDO::PARAM_INT)
            ->getAll();
    }
}
