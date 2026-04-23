<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover el prefijo del proyecto si existe
        $basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));
        $path = substr($path, strlen($basePath));
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                $controller = new $route['controller']();
                $params = $this->extractParams($route['path'], $path);
                return $controller->{$route['action']}(...$params);
            }
        }
        
        // 404
        http_response_code(404);
        require_once __DIR__ . '/../../views/errors/404.php';
    }

    private function matchPath($routePath, $requestPath) {
        $pattern = preg_replace('/\{[^\}]+\}/', '([^/]+)', $routePath);
        return preg_match('#^' . $pattern . '$#', $requestPath);
    }

    private function extractParams($routePath, $requestPath) {
        preg_match_all('/\{([^\}]+)\}/', $routePath, $paramNames);
        $pattern = preg_replace('/\{[^\}]+\}/', '([^/]+)', $routePath);
        preg_match('#^' . $pattern . '$#', $requestPath, $paramValues);
        
        array_shift($paramValues);
        return $paramValues;
    }
}