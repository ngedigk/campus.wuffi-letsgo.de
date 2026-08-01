<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\CsrfService;

class HomeController
{
    public function __construct(
        private DashboardController $dashboardController,
        private AuthController $authController,
        private AuthService $authService,
        private CsrfService $csrfService
    ) {}

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? null) === 'redeem') {
            $this->dashboardController->redeem();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? null) === 'logout') {
            $this->authController->logout();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authController->login();
            return;
        }

        $context = $this->buildContext();
        if ($this->authService->isLoggedIn()) {
            $this->dashboardController->index($context);
            return;
        }

        $this->authController->showLogin($context);
    }

    private function buildContext(): array
    {
        $context = [
            'csrfToken' => $this->csrfService->generateToken(),
            'user' => $this->authService->currentUser(),
            'isLoggedIn' => $this->authService->isLoggedIn(),
            'isAdmin' => $this->authService->isAdmin(),
            'loginError' => $_SESSION['login_error'] ?? null,
            'redeemError' => $_SESSION['redeem_error'] ?? null,
            'redeemSuccess' => $_SESSION['redeem_success'] ?? null,
            'additionalCss' => [],
        ];

        unset(
            $_SESSION['login_error'],
            $_SESSION['redeem_error'],
            $_SESSION['redeem_success']
        );

        return $context;
    }
}