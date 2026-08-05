<?php

namespace App\Routing;

use App\Container\Container;

class Route
{
    private array $middleware = [];

    public function __construct(
        private $handler
    ) {
    }

    public function middleware(string $middleware): self
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    public function run(Container $container, array $parameters = []): mixed
    {
        foreach ($this->middleware as $middlewareClass) {
            $middleware = $container->get($middlewareClass);
            $middleware->handle();
        }

        if (is_array($this->handler)) {
            [$controllerClass, $method] = $this->handler;

            $controller = $container->get($controllerClass);

            return call_user_func_array(
                [$controller, $method],
                array_values($parameters)
            );
        }

        return call_user_func_array(
            $this->handler,
            array_values($parameters)
        );
    }
}