<?php
/**
 * API Response Handler
 * Chuẩn hóa response format
 */

class Response {
    
    /**
     * Success response
     */
    public static function success($data = null, $message = "Success", $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Error response
     */
    public static function error($message = "Error", $statusCode = 400, $errors = null) {
        http_response_code($statusCode);
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Paginated response
     */
    public static function paginated($data, $total, $page, $pageSize, $message = "Success") {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPages' => ceil($total / $pageSize)
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Not found response
     */
    public static function notFound($message = "Resource not found") {
        self::error($message, 404);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized($message = "Unauthorized") {
        self::error($message, 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden($message = "Forbidden") {
        self::error($message, 403);
    }

    /**
     * Validation error response
     */
    public static function validationError($errors, $message = "Validation failed") {
        self::error($message, 422, $errors);
    }

    /**
     * Server error response
     */
    public static function serverError($message = "Internal server error") {
        self::error($message, 500);
    }
}
?>
