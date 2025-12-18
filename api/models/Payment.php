<?php
/**
 * Payment Model
 * Quản lý thanh toán
 */

class Payment extends BaseModel {
    
    protected $table = 'payments';
    
    /**
     * Get payment by order ID
     */
    public function getByOrderId($orderId) {
        $query = "SELECT * FROM {$this->table} WHERE order_id = :orderId LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get payment by transaction code
     */
    public function getByTransactionCode($transactionCode) {
        $query = "SELECT * FROM {$this->table} WHERE transaction_code = :transactionCode LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':transactionCode', $transactionCode);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get payment with order info
     */
    public function getPaymentWithOrder($paymentId) {
        $query = "SELECT p.*, o.total_amount as order_total, o.status as order_status, 
                         c.full_name, c.email, c.phone_number
                  FROM {$this->table} p
                  LEFT JOIN orders o ON p.order_id = o.order_id
                  LEFT JOIN customers c ON o.customer_id = c.customer_id
                  WHERE p.payment_id = :paymentId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':paymentId', $paymentId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Check if transaction code exists
     */
    public function transactionExists($transactionCode) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE transaction_code = :transactionCode";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':transactionCode', $transactionCode);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['count'] > 0;
    }
    
    /**
     * Get payments by date range
     */
    public function getPaymentsByDateRange($startDate, $endDate, $page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        
        $query = "SELECT p.*, o.order_id, c.full_name
                  FROM {$this->table} p
                  LEFT JOIN orders o ON p.order_id = o.order_id
                  LEFT JOIN customers c ON o.customer_id = c.customer_id
                  WHERE p.payment_date BETWEEN :startDate AND :endDate
                  ORDER BY p.payment_date DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total payments amount by date range
     */
    public function getTotalByDateRange($startDate, $endDate) {
        $query = "SELECT SUM(amount) as total 
                  FROM {$this->table} 
                  WHERE payment_date BETWEEN :startDate AND :endDate";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return floatval($row['total'] ?? 0);
    }
    
    /**
     * Get payments by method
     */
    public function getPaymentsByMethod($method, $page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE payment_method = :method 
                  ORDER BY payment_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
