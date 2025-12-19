<?php

namespace core;

/**
 * Simple Router Class
 */
class Router
{
    private $routes = [];
    private $currentRoute;

    /**
     * Add GET route
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
        return $this;
    }

    /**
     * Add POST route
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
        return $this;
    }

    /**
     * Add PUT route
     */
    public function put($path, $callback)
    {
        $this->routes['PUT'][$path] = $callback;
        return $this;
    }

    /**
     * Add DELETE route
     */
    public function delete($path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
        return $this;
    }

    /**
     * Dispatch request
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $uri = str_replace($basePath, '', $uri);
        $uri = rtrim($uri, '/') ?: '/';

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            return $this->executeRoute($this->routes[$method][$uri]);
        }

        // Check for parameterized routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?<\1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Filter matches to only named groups
                $params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);
                return $this->executeRoute($callback, $params);
            }
        }

        // 404 Not Found
        http_response_code(404);
        die('404 - Route not found: ' . $uri);
    }

    /**
     * Execute route callback
     */
    private function executeRoute($callback, $params = [])
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($controller, $method) = explode('@', $callback);
            $controllerClass = 'controllers\\' . $controller;

            if (!class_exists($controllerClass)) {
                die('Controller not found: ' . $controllerClass);
            }

            $controllerInstance = new $controllerClass();

            if (!method_exists($controllerInstance, $method)) {
                die('Method not found: ' . $method . ' in ' . $controllerClass);
            }

            return call_user_func_array([$controllerInstance, $method], $params);
        }

        die('Invalid route callback');
    }
}
