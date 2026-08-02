<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\CsrfService;
use App\Helpers\ViewRenderer;

use \Exception;

class AuthController
{
    
    public function __construct(
        private ViewRenderer $viewRenderer,
        private AuthService $authService,
        private CsrfService $csrfService
    ) {}

    public function login(): void
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            $_SESSION['login_error'] = 'Bitte füllen Sie alle Felder aus.';
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token']);
        } catch (Exception $e) {
            $_SESSION['login_error'] = $e->getMessage();
        }

        $result = $this->authService->login(
            trim($_POST['email']),
            $_POST['password']
        );

        if (!$result->success) {
            $_SESSION['login_error'] = $result->error;
        }

        header('Location: index.php');
        exit;
    }

    public function showLogin(array $context): void
    {
        $context['additionalJs'] = [
            ['src' => '/assets/js/password-toggle.js']
        ];

        $viewData = [
            'pageTitle' => 'Login',
            ...$context,
        ];

        $this->viewRenderer->renderWithTemplate('login-form', $viewData);
    }

    public function logout(): void
    {
        $this->authService->logout();
        header('Location: index.php');
        exit;
    }
}