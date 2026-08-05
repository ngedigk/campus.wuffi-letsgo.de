<?php

namespace App\Container;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

use App\Services\AuthService;

trait MiddlewareBindings
{
    private function registerMiddleware(): void
    {
        $this->set(AuthMiddleware::class, fn ($c) => new AuthMiddleware($c->get(AuthService::class)));
        $this->set(AdminMiddleware::class, fn ($c) => new AdminMiddleware($c->get(AuthService::class)));
    }
}