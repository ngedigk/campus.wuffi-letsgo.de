<?php

namespace App\Middleware;

use App\Services\AuthService;

class AuthMiddleware
{
    public function __construct(
        private AuthService $authService
    ) {
    }

    public function handle(): void
    {
        $this->authService->requireLogin();
    }
}