<?php
/**
 * Promotion Model
 * Quản lý mã khuyến mãi
 */

class Promotion extends BaseModel {
    
    protected $table = 'promotions';
    
    /**
     * Get all active promotions
     */
    public function getActivePromotions($page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE start_date <= :now AND end_date >= :now 
                  ORDER BY start_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get active promotions count
     */
    public function getActivePromotionsCount() {
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE start_date <= :now AND end_date >= :now";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return (int) $row['total'];
    }
    
    /**
     * Get promotion by code
     */
    public function getByCode($code) {
        $query = "SELECT * FROM {$this->table} WHERE code = :code LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Validate promotion code
     */
    public function validateCode($code) {
        $promotion = $this->getByCode($code);
        
        if (!$promotion) {
            return [
                'valid' => false,
                'message' => 'Mã khuyến mãi không tồn tại'
            ];
        }
        
        $now = date('Y-m-d H:i:s');
        
        // Check if promotion is active
        if ($promotion['start_date'] > $now) {
            return [
                'valid' => false,
                'message' => 'Mã khuyến mãi chưa có hiệu lực'
            ];
        }
        
        if ($promotion['end_date'] < $now) {
            return [
                'valid' => false,
                'message' => 'Mã khuyến mãi đã hết hạn'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Mã khuyến mãi hợp lệ',
            'promotion' => $promotion
        ];
    }
    
    /**
     * Calculate discount amount
     */
    public function calculateDiscount($promotionId, $totalAmount) {
        $promotion = $this->getById($promotionId);
        
        if (!$promotion) {
            return 0;
        }
        
        $discountAmount = 0;
        
        if ($promotion['discount_type'] === 'percentage') {
            // Percentage discount
            $discountAmount = ($totalAmount * $promotion['discount_value']) / 100;
        } else if ($promotion['discount_type'] === 'fixed') {
            // Fixed amount discount
            $discountAmount = $promotion['discount_value'];
        }
        
        // Ensure discount doesn't exceed total
        return min($discountAmount, $totalAmount);
    }
    
    /**
     * Check if promotion code exists
     */
    public function codeExists($code, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE code = :code";
        
        if ($excludeId) {
            $query .= " AND promotion_id != :excludeId";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId);
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        
        return $row['count'] > 0;
    }
    
    /**
     * Get promotions by date range
     */
    public function getByDateRange($startDate, $endDate, $page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE (start_date BETWEEN :startDate AND :endDate)
                     OR (end_date BETWEEN :startDate AND :endDate)
                     OR (:startDate BETWEEN start_date AND end_date)
                  ORDER BY start_date DESC
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
     * Get upcoming promotions
     */
    public function getUpcomingPromotions($limit = 10) {
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE start_date > :now 
                  ORDER BY start_date ASC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get expired promotions
     */
    public function getExpiredPromotions($page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE end_date < :now 
                  ORDER BY end_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        $stmt->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
