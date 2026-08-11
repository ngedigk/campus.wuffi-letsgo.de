<?php

namespace App\Controller;

use App\Services\PasswordResetService;
use App\Services\CsrfService;
use App\Services\AuthService;

use App\Helpers\ViewRenderer;

use App\Exceptions\CsrfException;
use \Throwable;

class ResetPasswordController
{
    public function __construct(
        private PasswordResetService $passwordResetService,
        private CsrfService $csrfService,
        private ViewRenderer $viewRenderer,
        private AuthService $authService
    ) {}

    public function index(): void
    {
        $token = $_GET['token'] ?? '';
        $userUuid = $this->passwordResetService->getUserUuidByToken($token);

        if (!$userUuid) {
            http_response_code(404);
            echo "Invalid or expired token.";
            exit;
        }

        $this->renderForm([], $userUuid);
    }

    public function reset(): void
    {
        $token = $_GET['token'] ?? '';
        $userUuid = $this->passwordResetService->getUserUuidByToken($token);

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $this->renderForm(['error' => $e->getMessage()], $userUuid);
            return;
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($password !== $passwordConfirm) {
            $this->renderForm(['error' => 'Passwörter stimmen nicht überein.'], $userUuid);
            return;
        }

        try {
            $this->passwordResetService->resetPassword($userUuid, $password);

            $this->renderForm(['success' => 'Passwort erfolgreich aktualisiert.'], $userUuid);
        } catch (Throwable $e) {
            error_log($e);
            
            $this->renderForm(['error' => 'Bei der Aktualisierung des Passworts ist ein Fehler aufgetreten.'], $userUuid);
        }
    }

    private function renderForm(array $context, string $userUuid): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Reset Password';
        $context['isLoggedIn'] = $this->authService->isLoggedIn();
        $context['additionalCss'] = ['/assets/css/password-meter.css'];
        $context['additionalJs'] = [
            ['src' => '/assets/js/password-toggle.js'],
            ['src' => '/assets/js/password-meter.js']
        ];

        $this->viewRenderer->renderWithTemplate('reset-password', $context);
    }
}