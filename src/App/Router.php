<?php

namespace App;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, array $handler): void {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, array $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void {
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Remove trailing slash if present (except for root '/')
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        $matchMethod = ($method === 'HEAD') ? 'GET' : $method;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $matchMethod) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $route['handler'];

                $controller = new $class();
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        if (str_starts_with($path, '/api/')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Endpoint not found', 'path' => $path]);
        } else {
            echo "<h1>404 Page Not Found</h1><p>The requested URL {$path} was not found on this server.</p>";
        }
    }
}
