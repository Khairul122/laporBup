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

    public function put(string $path, string $handler): void {
        $this->register('PUT', $path, $handler);
    }

    public function patch(string $path, string $handler): void {
        $this->register('PATCH', $path, $handler);
    }

    public function delete(string $path, string $handler): void {
        $this->register('DELETE', $path, $handler);
    }

    public function any(string $path, string $handler): void {
        $this->register('ANY', $path, $handler);
    }

    public function dispatch(): void {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper((string) $_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $requestMethod = $override;
            }
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '';
        if (defined('BASE_PATH') && BASE_PATH !== '' && strpos($path, BASE_PATH) === 0) {
            $path = substr($path, strlen(BASE_PATH));
        }
        $requestPath = trim($path, '/');

        foreach ($this->routes as $route) {
            $pattern = $this->compilePattern($route['path']);
            if (!preg_match($pattern, $requestPath, $matches)) {
                continue;
            }

            if ($route['method'] !== 'ANY' && $route['method'] !== $requestMethod) {
                continue;
            }

            array_shift($matches);
            $this->invoke($route['handler'], $matches);
            return;
        }

        http_response_code(404);
        require 'views/errors/404.php';
    }

    private function compilePattern(string $path): string {
        if ($path === '') {
            return '#^$#';
        }

        $escapedPath = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', static function () {
            return '([^/]+)';
        }, preg_quote($path, '#'));

        $escapedPath = str_replace('\(\[\^/\]\+\)', '([^/]+)', $escapedPath);
        return '#^' . $escapedPath . '$#';
    }

    private function invoke(string $handler, array $params = []): void {
        [$class, $method] = explode('@', $handler);
        $file = 'controllers/' . $class . '.php';

        require_once $file;
        call_user_func_array([new $class(), $method], $params);
    }
}
