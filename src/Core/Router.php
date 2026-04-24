<?php
namespace App\Core;

class Router {
    private $routes = [];

    /**
     * Registrar una nueva ruta
     */
    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Compara una ruta patrón con la URI solicitada
     * Soporta parámetros dinámicos: /products/{id}
     */
    private function matchPath(string $pattern, string $path): bool {
        // Normalizar rutas (quitar slash final si existe)
        $pattern = rtrim($pattern, '/');
        $path = rtrim($path, '/');
        
        // Si son idénticas, match directo
        if ($pattern === $path) {
            return true;
        }
        
        // Escapar caracteres especiales de regex en el patrón
        $regexPattern = preg_quote($pattern, '#');
        
        // Reemplazar {param} por un patrón que capture cualquier valor excepto /
        $regex = '#^' . preg_replace('/\\\{[a-zA-Z0-9_]+\\\}/', '([^/]+)', $regexPattern) . '$#';
        
        return (bool) preg_match($regex, $path);
    }

    /**
     * Extrae los parámetros dinámicos de la URI
     * Ej: pattern=/products/{id}, path=/products/5 → ['id' => '5']
     */
    private function extractParams(string $pattern, string $path): array {
        $params = [];
        
        // Normalizar
        $pattern = rtrim($pattern, '/');
        $path = rtrim($path, '/');
        
        // Extraer nombres de parámetros del patrón
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $pattern, $paramNames);
        
        if (empty($paramNames[1])) {
            return []; // No hay parámetros dinámicos
        }
        
        // Escapar y convertir patrón a regex para capturar valores
        $regexPattern = preg_quote($pattern, '#');
        $regex = '#^' . preg_replace('/\\\{[a-zA-Z0-9_]+\\\}/', '([^/]+)', $regexPattern) . '$#';
        
        if (preg_match($regex, $path, $matches)) {
            array_shift($matches); // Remover el match completo (índice 0)
            // Combinar nombres con valores capturados
            $params = array_combine($paramNames[1], $matches);
        }
        
        return $params ?? [];
    }

    /**
     * Procesar la solicitud y ejecutar el controlador correspondiente
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Obtener la ruta base del proyecto
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);
        
        // Quitar la parte del directorio del script de la URI
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $path = substr($uri, strlen($scriptDir));
        } else {
            $path = $uri;
        }
        
        // Si la ruta está vacía, ponerla como "/"
        if (empty($path)) {
            $path = '/';
        }
        
        // Asegurar que empiece con /
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        
        // Buscar coincidencia en las rutas registradas
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                $controller = new $route['controller']();
                $params = $this->extractParams($route['path'], $path);
                
                // Pasar parámetros como array asociativo o desestructurados
                return $controller->{$route['action']}(...array_values($params));
            }
        }
        
        // 404 - No se encontró la ruta
        http_response_code(404);
        require_once __DIR__ . '/../../views/errors/404.php';
        exit;
    }
}