<?php
/**
 * Router Class
 * 
 * This class handles routing for the application.
 * It maps URLs to controller actions and dispatches requests.
 */
class Router {
    /**
     * @var array The registered routes
     */
    private $routes = [];
    
    /**
     * @var callable|null The 404 handler
     */
    private $notFoundHandler = null;
    
    /**
     * Add a route to the router
     * 
     * @param string $path The route path
     * @param string|callable $handler The controller and method to handle the route (e.g., 'HomeController@index') or a callback function
     * @param string $method The HTTP method (GET, POST, etc.)
     * @return void
     */
    public function addRoute($path, $handler, $method = 'GET') {
        $this->routes[] = [
            'path' => $path,
            'handler' => $handler,
            'method' => strtoupper($method)
        ];
    }
    
    /**
     * Add a GET route to the router
     * 
     * @param string $path The route path
     * @param string|callable $handler The controller and method or a callback function
     * @return void
     */
    public function get($path, $handler) {
        $this->addRoute($path, $handler, 'GET');
    }
    
    /**
     * Add a POST route to the router
     * 
     * @param string $path The route path
     * @param string|callable $handler The controller and method or a callback function
     * @return void
     */
    public function post($path, $handler) {
        $this->addRoute($path, $handler, 'POST');
    }
    
    /**
     * Set the 404 handler
     * 
     * @param callable $handler The handler function
     * @return void
     */
    public function setNotFoundHandler($handler) {
        $this->notFoundHandler = $handler;
    }
    
    /**
     * Dispatch the request to the appropriate controller
     * 
     * @return void
     */
    public function dispatch() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remove query string from the request URI if present
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        // Remove trailing slash if present (except for the root path)
        if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
            $requestUri = rtrim($requestUri, '/');
        }
        
        // If empty URI, set to '/'
        if (empty($requestUri)) {
            $requestUri = '/';
        }
        
        foreach ($this->routes as $route) {
            // Skip routes that don't match the request method
            if ($route['method'] !== $requestMethod && $route['method'] !== 'ANY') {
                continue;
            }
            
            // Check for direct match
            if ($route['path'] === $requestUri) {
                $this->executeHandler($route['handler']);
                return;
            }
            
            // Check for pattern matches
            $pattern = $this->convertRouteToRegex($route['path']);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                // Remove the full match from the matches array
                array_shift($matches);
                
                // Extract parameter names
                preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route['path'], $paramNames);
                $paramNames = isset($paramNames[1]) ? $paramNames[1] : [];
                
                // Combine parameter names with values
                $params = [];
                foreach ($paramNames as $index => $name) {
                    $params[$name] = isset($matches[$index]) ? $matches[$index] : null;
                }
                
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }
        
        // If no route matches, call the 404 handler
        $this->handleNotFound();
    }
    
    /**
     * Convert a route path to a regular expression
     * 
     * @param string $route The route path
     * @return string The regular expression
     */
    private function convertRouteToRegex($route) {
        // Replace route parameters with regex patterns
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        
        // Escape forward slashes and add start/end anchors
        $pattern = '#^' . $pattern . '$#';
        
        return $pattern;
    }
    
    /**
     * Execute the route handler
     * 
     * @param string|callable $handler The controller and method (format: Controller@method) or a callback function
     * @param array $params The route parameters
     * @return void
     */
    private function executeHandler($handler, $params = []) {
        // If handler is a callback function
        if (is_callable($handler)) {
            if (empty($params)) {
                call_user_func($handler);
            } else {
                call_user_func_array($handler, $params);
            }
            return;
        }
        
        // If handler is a string in the format Controller@method
        list($controller, $method) = explode('@', $handler);
        
        $controllerInstance = new $controller();
        
        // Call the method with the parameters
        if (empty($params)) {
            call_user_func([$controllerInstance, $method]);
        } else {
            call_user_func_array([$controllerInstance, $method], $params);
        }
    }
    
    /**
     * Handle a 404 Not Found error
     * 
     * @return void
     */
    private function handleNotFound() {
        // Set the HTTP response code to 404
        http_response_code(404);
        
        // Call the custom 404 handler if set
        if ($this->notFoundHandler !== null) {
            call_user_func($this->notFoundHandler);
            return;
        }
        
        // Default 404 handler
        if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0) {
            // Admin 404 page
            if (defined('ADMIN_ROOT') && file_exists(ADMIN_ROOT . '/templates/404.php')) {
                include ADMIN_ROOT . '/templates/404.php';
            } else {
                // Fallback to front-end 404 page
                $pageTitle = '404 Not Found';
                $contentTemplate = __DIR__ . '/../templates/404.php';
                include __DIR__ . '/../templates/layout.php';
            }
        } else {
            // Front-end 404 page
            $pageTitle = '404 Not Found';
            $contentTemplate = __DIR__ . '/../templates/404.php';
            include __DIR__ . '/../templates/layout.php';
        }
        
        exit;
    }
}
?>