<?php
/**
 * CORS Middleware
 * Xử lý Cross-Origin Resource Sharing
 */

class CORS {
    
    /**
     * Handle CORS
     */
    public static function handle() {
        // Get origin
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        // Check if origin is allowed
        $allowedOrigins = ALLOWED_ORIGINS;
        
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            // In development, allow all origins
            header("Access-Control-Allow-Origin: *");
        }
        
        // Allow credentials
        header("Access-Control-Allow-Credentials: true");
        
        // Allow methods
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        
        // Allow headers
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        
        // Set max age
        header("Access-Control-Max-Age: 3600");
        
        // Set content type
        header("Content-Type: application/json; charset=UTF-8");
        
        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
?>
