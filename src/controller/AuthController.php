<?php

class AuthController
{
    
    public function __construct(
        private ViewRenderer $viewRenderer,
        private AuthService $authService
    ) {}

    public function login(): void
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            $_SESSION['login_error'] = 'Bitte füllen Sie alle Felder aus.';
            return;
        }

        validateCsrf();

        $result = $this->authService->login(
            trim($_POST['email']),
            $_POST['password']
        );

        if (!$result->success) {
            $_SESSION['login_error'] = $result->error;
            return;
        }

        header('Location: index.php');
        exit;
    }

    public function showLogin(array $context): void
    {
        $viewData = [
            'pageTitle' => 'Login',
            ...$context,
        ];

        $this->viewRenderer->renderWithTemplate('login-form', $viewData);
    }
}