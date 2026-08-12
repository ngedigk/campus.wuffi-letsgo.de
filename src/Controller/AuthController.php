<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\CsrfService;

use App\Helpers\ViewRenderer;

use Psr\Log\LoggerInterface;

use App\Exceptions\CsrfException;

class AuthController
{
    
    public function __construct(
        private ViewRenderer $viewRenderer,
        private AuthService $authService,
        private CsrfService $csrfService,
        private LoggerInterface $logger
    ) {}

    public function login(): void
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            $_SESSION['login_error'] = 'Bitte füllen Sie alle Felder aus.';
            header('Location: /');
            exit;
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $this->logger->error('CSRF validation failed', ['exception' => $e]);
            $_SESSION['login_error'] = 'Die Anfrage konnte nicht validiert werden.';
            header('Location: /');
            exit;
        }

        $result = $this->authService->login(
            trim($_POST['email']),
            $_POST['password']
        );

        if (!$result->success) {
            $_SESSION['login_error'] = $result->error;
        }

        header('Location: /');
        exit;
    }

    public function showLogin(array $context): void
    {
        $context['additionalJs'] = [
            ['src' => '/assets/js/password-toggle.js']
        ];

        $viewData = array_merge(
            ['pageTitle' => 'Login'],
            $context
        );

        $this->viewRenderer->renderWithTemplate('login-form', $viewData);
    }

    public function logout(): void
    {
        $this->authService->logout();
        header('Location: /');
        exit;
    }
}