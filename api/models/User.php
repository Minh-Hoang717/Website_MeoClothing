<?php
/**
 * User Model
 * Quản lý users (customers, staff, admin)
 * Replaces old Customer and Employee models
 */

class User extends BaseModel {
    
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE username = :username LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE role = :role 
                  ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all customers
     */
    public function getCustomers() {
        return $this->getByRole('customer');
    }
    
    /**
     * Get all staff
     */
    public function getStaff() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE role IN ('admin', 'staff') 
                  ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new user
     */
    public function createUser($data) {
        $query = "INSERT INTO " . $this->table . " 
                  SET username = :username,
                      password = :password,
                      full_name = :full_name,
                      email = :email,
                      phone = :phone,
                      address = :address,
                      role = :role";
        
        $stmt = $this->db->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']); // Should be hashed
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $role = $data['role'] ?? 'customer';
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE username = :username";
        
        if ($excludeId) {
            $query .= " AND user_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE email = :email";
        
        if ($excludeId) {
            $query .= " AND user_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($username, $password) {
        $user = $this->getByUsername($username);
        
        if (!$user) {
            return false;
        }
        
        // If password is hashed, use password_verify
        // For now, simple comparison (should use password_hash in production)
        return $user['password'] === $password;
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $fields = [];
        $params = [':user_id' => $userId];
        
        if (isset($data['full_name'])) {
            $fields[] = "full_name = :full_name";
            $params[':full_name'] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }
        
        if (isset($data['address'])) {
            $fields[] = "address = :address";
            $params[':address'] = $data['address'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET " . implode(', ', $fields) . " 
                  WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute($params);
    }
}
?>
