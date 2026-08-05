<?php

namespace App\Middleware;

use App\Services\AuthService;

class AdminMiddleware
{
    public function __construct(
        private AuthService $authService
    ) {
    }

    public function handle(): void
    {
        if (!$this->authService->isAdmin()) {
            $_SESSION['admin_error'] =
                'Sie haben keine Berechtigung, administrative Funktionen zu verwalten.';

            header('Location: /');
            exit;
        }
    }
}