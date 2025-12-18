<?php
/**
 * Base Controller
 * Controller cơ sở cho tất cả các controller khác
 */

abstract class BaseController {
    
    protected $db;
    protected $requestMethod;
    protected $requestData;
    
    public function __construct($db) {
        $this->db = $db;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestData = $this->getRequestData();
    }
    
    /**
     * Get request data (JSON body or form data)
     */
    protected function getRequestData() {
        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        // If not JSON, try form data
        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = $_REQUEST;
        }
        
        return $data ?? [];
    }
    
    /**
     * Get query parameters
     */
    protected function getQueryParams() {
        return $_GET;
    }
    
    /**
     * Get path parameter by index
     */
    protected function getPathParam($index) {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($uri, '/'));
        
        return $parts[$index] ?? null;
    }
    
    /**
     * Get pagination parameters
     */
    protected function getPaginationParams() {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : DEFAULT_PAGE_SIZE;
        
        // Validate
        $page = max(1, $page);
        $pageSize = min(MAX_PAGE_SIZE, max(1, $pageSize));
        
        return [
            'page' => $page,
            'pageSize' => $pageSize
        ];
    }
    
    /**
     * Process request (to be implemented by child classes)
     */
    abstract public function processRequest();
    
    /**
     * Handle method not allowed
     */
    protected function methodNotAllowed() {
        Response::error("Method not allowed", 405);
    }
}
?>
