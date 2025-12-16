<?php
/**
 * API Entry Point
 * Main router cho tất cả API requests
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configurations
require_once 'config/config.php';
require_once 'config/database.php';

// Load utilities
require_once 'utils/Response.php';
require_once 'utils/Validator.php';

// Load middleware
require_once 'middleware/CORS.php';
require_once 'middleware/Auth.php';

// Load base classes
require_once 'models/BaseModel.php';
require_once 'controllers/BaseController.php';

// Handle CORS
CORS::handle();

// Get database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    Response::serverError("Database connection failed");
}

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove base path if needed (e.g., /api)
$requestUri = str_replace('/api', '', $requestUri);
$requestUri = trim($requestUri, '/');

// Route dispatcher
try {
    // Split URI into parts
    $uriParts = explode('/', $requestUri);
    $resource = $uriParts[0] ?? '';
    
    // Route to appropriate controller
    switch ($resource) {
        case 'promotions':
            require_once 'models/Promotion.php';
            require_once 'controllers/PromotionController.php';
            $controller = new PromotionController($db);
            $controller->processRequest();
            break;
            
        case 'payments':
            require_once 'models/Payment.php';
            require_once 'models/Order.php';
            require_once 'controllers/PaymentController.php';
            $controller = new PaymentController($db);
            $controller->processRequest();
            break;
            
        case 'reports':
            require_once 'controllers/ReportController.php';
            $controller = new ReportController($db);
            $controller->processRequest();
            break;
            
        case 'test':
            // Test endpoint
            Response::success([
                'message' => 'API is working!',
                'version' => API_VERSION,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;
            
        default:
            Response::notFound("Endpoint not found: /$resource");
            break;
    }
    
} catch (Exception $e) {
    Response::serverError("Server error: " . $e->getMessage());
}

// Close database connection
$database->closeConnection();
?>
