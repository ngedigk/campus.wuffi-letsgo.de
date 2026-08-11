<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\PasswordResetService;

use App\Helpers\ViewRenderer;

use App\Exceptions\CsrfException;

class ForgotPasswordController
{
    public function __construct(
        private AuthService $authService,
        private CsrfService $csrfService,
        private PasswordResetService $passwordResetService,
        private ViewRenderer $viewRenderer
    ) {}

    public function index(): void
    {
        $this->renderForm([]);
    }

    public function requestReset(): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $this->renderForm(['error' => $e->getMessage()]);
            return;
        }

        $email = trim($_POST['email'] ?? '');

        $this->passwordResetService->requestReset($email);

        $this->renderForm(['success' => 'Wenn die E-Mail-Adresse existiert, wurde ein Reset-Link gesendet.']);
    }

    private function renderForm(array $context): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Passwort vergessen';
        $context['isLoggedIn'] = $this->authService->isLoggedIn();

        $this->viewRenderer->renderWithTemplate('forgot-password', $context);
    }
}