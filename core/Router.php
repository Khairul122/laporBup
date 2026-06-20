<?php

class Router {

    private array $routes = [];

    public function register(string $method, string $path, string $handler): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => trim($path, '/'),
            'handler' => $handler
        ];
    }

    public function get(string $path, string $handler): void {
        $this->register('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void {
        $this->register('POST', $path, $handler);
    }

    public function any(string $path, string $handler): void {
        $this->register('ANY', $path, $handler);
    }

    public function dispatch(): void {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $requestPath = '';

        if (isset($_GET['controller'])) {
            $controller = $_GET['controller'];
            $action = $_GET['action'] ?? 'index';
            $requestPath = trim($controller, '/') . '/' . trim($action, '/');
        } else {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            $path = parse_url($requestUri, PHP_URL_PATH);
            if (defined('BASE_PATH') && BASE_PATH !== '') {
                if (strpos($path, BASE_PATH) === 0) {
                    $path = substr($path, strlen(BASE_PATH));
                }
            }
            $requestPath = trim($path, '/');
        }

        foreach ($this->routes as $route) {
            $routePath = $route['path'];
            $match = ($routePath === $requestPath);

            if (!$match) {
                if ($routePath === '' && $requestPath === 'index.php') {
                    $match = true;
                } elseif ($routePath === 'auth' && ($requestPath === '' || $requestPath === 'auth/index')) {
                    $match = true;
                } elseif ($routePath === $requestPath . '/index') {
                    $match = true;
                } elseif ($routePath . '/index' === $requestPath) {
                    $match = true;
                }
            }

            if ($match) {
                if ($route['method'] !== 'ANY' && $route['method'] !== $requestMethod) {
                    continue;
                }

                $this->invoke($route['handler']);
                return;
            }
        }

        http_response_code(404);
        require 'views/errors/404.php';
    }

    private function invoke(string $handler): void {
        [$class, $method] = explode('@', $handler);
        $file = 'controllers/' . $class . '.php';

        require_once $file;
        (new $class())->$method();
    }
}
