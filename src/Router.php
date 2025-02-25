<?php

namespace App;

class Router {
    private array $routes = [];

    public function addRoute(string $method, string $path, $handler): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function get(string $path, $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    public function delete(string $path, $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function dispatch(string $method, string $uri) {
        $method = strtoupper($method);
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                // echo "Matched route: {$route['path']} with method: {$route['method']}\n";
                $params = $this->getParams($route['path'], $uri);
                
                if (is_array($route['handler'])) {
                    [$controller, $action] = $route['handler'];
                    $controllerObj = new $controller();
                    return call_user_func_array([$controllerObj, $action], $params);
                }
                
                return call_user_func($route['handler']);
            }
        }
        
        throw new \Exception('Route not found', 404);
    }

    private function matchPath(string $routePath, string $uri): bool {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '@^' . str_replace('/', '\/', $pattern) . '$@';

        return (bool) preg_match($pattern, $uri);
    }

    private function getParams(string $routePath, string $uri): array {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '@^' . str_replace('/', '\/', $pattern) . '$@';
        
        preg_match($pattern, $uri, $matches);
        array_shift($matches); 
        
        return $matches;
    }
}
