<?php

class HomeController
{
    public function __construct(
        private DashboardController $dashboardController,
        private AuthController $authController,
        private AuthService $authService
    ) {}

    public function index(): void
    {
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