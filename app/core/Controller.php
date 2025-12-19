<?php

namespace core;

/**
 * Base Controller Class
 */
abstract class Controller
{
    protected $db;
    protected $data = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Load View
     */
    protected function view($viewPath, $data = [])
    {
        $this->data = $data;
        extract($this->data);
        
        $viewFile = APP_PATH . '/views/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            die('View not found: ' . $viewFile);
        }
        
        require $viewFile;
    }

    /**
     * Load Layout with View
     */
    protected function render($viewPath, $data = [])
    {
        $this->data = $data;
        extract($this->data);
        
        ob_start();
        
        $viewFile = APP_PATH . '/views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            die('View not found: ' . $viewFile);
        }
        
        require $viewFile;
        $content = ob_get_clean();
        
        require APP_PATH . '/views/layouts/main.php';
    }

    /**
     * JSON Response
     */
    protected function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
