<?php
/**
 * Order Model
 * Quản lý đơn hàng
 */

class Order extends BaseModel {
    
    protected $table = 'orders';
    
    /**
     * Get order with customer info
     */
    public function getOrderWithCustomer($orderId) {
        $query = "SELECT o.*, c.full_name, c.phone_number, c.email, c.address 
                  FROM {$this->table} o
                  LEFT JOIN customers c ON o.customer_id = c.customer_id
                  WHERE o.order_id = :orderId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get order details (items)
     */
    public function getOrderDetails($orderId) {
        $query = "SELECT od.*, pv.size, pv.color, pv.sku, p.name as product_name
                  FROM orderdetails od
                  LEFT JOIN productvariants pv ON od.variant_id = pv.variant_id
                  LEFT JOIN products p ON pv.product_id = p.product_id
                  WHERE od.order_id = :orderId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create order with details
     */
    public function createOrderWithDetails($orderData, $orderDetails) {
        try {
            $this->conn->beginTransaction();
            
            // Create order
            $orderId = $this->create($orderData);
            
            if (!$orderId) {
                throw new Exception("Không thể tạo đơn hàng");
            }
            
            // Create order details
            $query = "INSERT INTO orderdetails (order_id, variant_id, quantity, unit_price, discount_amount) 
                      VALUES (:order_id, :variant_id, :quantity, :unit_price, :discount_amount)";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($orderDetails as $detail) {
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':variant_id' => $detail['variant_id'],
                    ':quantity' => $detail['quantity'],
                    ':unit_price' => $detail['unit_price'],
                    ':discount_amount' => $detail['discount_amount'] ?? 0
                ]);
                
                // Update inventory
                $this->updateInventory($detail['variant_id'], -$detail['quantity']);
            }
            
            $this->conn->commit();
            
            return $orderId;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        $allowedStatuses = ['pending', 'confirmed', 'processing', 'shipping', 'completed', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception("Trạng thái không hợp lệ");
        }
        
        return $this->update($orderId, ['status' => $status]);
    }
    
    /**
     * Update inventory
     */
    private function updateInventory($variantId, $quantityChange) {
        $query = "UPDATE inventory SET quantity = quantity + :change WHERE variant_id = :variantId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':change' => $quantityChange,
            ':variantId' => $variantId
        ]);
    }
    
    /**
     * Get orders by customer
     */
    public function getOrdersByCustomer($customerId, $page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE customer_id = :customerId 
                  ORDER BY order_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customerId', $customerId);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status, $page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        
        $query = "SELECT o.*, c.full_name, c.phone_number 
                  FROM {$this->table} o
                  LEFT JOIN customers c ON o.customer_id = c.customer_id
                  WHERE o.status = :status 
                  ORDER BY o.order_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
