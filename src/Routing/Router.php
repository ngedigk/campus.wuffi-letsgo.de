<?php

namespace App\Routing;

use App\Container\Container;

class Router
{
    private array $routes = [];

    private string $groupPrefix = '';

    private array $groupMiddleware = [];

    public function __construct(
        private Container $container
    ) {}

    public function get(string $path, callable|array $handler): Route
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): Route
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function group(array $options, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousPrefix . ($options['prefix'] ?? '');

        $this->groupMiddleware = array_merge(
            $previousMiddleware,
            $options['middleware'] ?? []
        );

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, callable|array $handler): Route
    {
        $path = $this->groupPrefix . $path;

        $route = new Route($handler);

        foreach ($this->groupMiddleware as $middleware) {
            $route->middleware($middleware);
        }

        $this->routes[$method][$path] = $route;

        return $route;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $match = $this->matchRoute($method, $path);

        if ($match === null) {
            http_response_code(404);
            echo '404 - Seite nicht gefunden';
            return null;
        }

        return $match['route']->run(
            $this->container,
            $match['parameters']
        );
    }

    private function matchRoute(string $method, string $path): ?array {
        foreach ($this->routes[$method] ?? [] as $routePath => $route) {
            $parameterNames = [];

            $pattern = preg_replace_callback(
                '/\{([^}]+)\}/',
                function ($matches) use (&$parameterNames) {
                    $parameterNames[] = $matches[1];
                    return '([^/]+)';
                },
                $routePath
            );

            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);

                $parameters = [];

                foreach ($parameterNames as $index => $name) {
                    $parameters[$name] = $matches[$index];
                }

                return [
                    'route' => $route,
                    'parameters' => $parameters,
                ];
            }
        }

        return null;
    }
}