<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, array $action, array $middlewares = []): void
    {
        $this->add('GET', $pattern, $action, $middlewares);
    }

    public function post(string $pattern, array $action, array $middlewares = []): void
    {
        $this->add('POST', $pattern, $action, $middlewares);
    }

    public function put(string $pattern, array $action, array $middlewares = []): void
    {
        $this->add('PUT', $pattern, $action, $middlewares);
    }

    public function delete(string $pattern, array $action, array $middlewares = []): void
    {
        $this->add('DELETE', $pattern, $action, $middlewares);
    }

    public function add(string $method, string $pattern, array $action, array $middlewares = []): void
    {
        $this->routes[] = compact('method', 'pattern', 'action', 'middlewares');
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[0-9]+)', $route['pattern']);
            $regex = '#^' . $regex . '$#';

            if (!preg_match($regex, $uri, $matches)) {
                continue;
            }

            Request::verifyCsrf();

            foreach ($route['middlewares'] as $middlewareClass => $arguments) {
                (new $middlewareClass())->handle(...$arguments);
            }

            [$controllerClass, $methodName] = $route['action'];
            $controller = new $controllerClass();
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $controller->{$methodName}(...array_values($params));
            return;
        }

        http_response_code(404);
        View::render('errors/404', [], 'layouts/minimal');
    }
}
