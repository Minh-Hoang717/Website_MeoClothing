<?php
/**
 * Authentication Middleware
 * Kiểm tra xác thực người dùng
 */

class Auth {
    
    /**
     * Verify JWT token (simplified version)
     */
    public static function verifyToken() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            Response::unauthorized("No token provided");
        }
        
        $authHeader = $headers['Authorization'];
        
        // Extract token from "Bearer <token>"
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            Response::unauthorized("Invalid token format");
        }
        
        // TODO: Implement actual JWT verification
        // For now, just check if token exists
        if (empty($token)) {
            Response::unauthorized("Invalid token");
        }
        
        return true;
    }
    
    /**
     * Check if user is admin/employee
     */
    public static function requireAdmin() {
        self::verifyToken();
        
        // TODO: Implement actual role checking from JWT or session
        // For now, assume verified
        
        return true;
    }
    
    /**
     * Get current user from token
     */
    public static function getCurrentUser() {
        // TODO: Extract user info from JWT
        return [
            'user_id' => 1,
            'role' => 'admin'
        ];
    }
}
?>
