<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\UserService;
use App\Services\MailerService;
use App\Services\PasswordResetsService;

use App\Helpers\ViewRenderer;

use \Exception;

class ForgotPasswordController
{
    public function __construct(
        private AuthService $authService,
        private CsrfService $csrfService,
        private UserService $userService,
        private PasswordResetsService $passwordResetsService,
        private MailerService $mailerService,
        private ViewRenderer $viewRenderer
    ) {}

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleForgotPassword();
            return;
        }

        $this->renderForm([]);
    }

    private function handleForgotPassword(): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->renderForm(['message' => $e->getMessage()]);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $user = $this->userService->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->passwordResetsService->recordReset($user->id, $token);
            $this->sendResetEmail($email, $token);
        }

        $this->renderForm(['message' => 'Wenn die E-Mail-Adresse existiert, wurde ein Reset-Link gesendet.']);
    }

    private function sendResetEmail(string $email, string $token): void
    {
        $link = SITE_URL . "/reset-password.php?token=" . urlencode($token);
        $htmlBody = "<p>Passwort zurücksetzen:</p><a href='" . htmlspecialchars($link) . "'>" . htmlspecialchars($link) . "</a>";

        try {
            $this->mailerService->send($email, 'Passwort zurücksetzen', $htmlBody);
        } catch (Exception $e) {
            error_log("Reset email failed: " . $e->getMessage());
        }
    }

    private function renderForm(array $context): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Passwort vergessen';
        $context['isLoggedIn'] = $this->authService->isLoggedIn();

        $this->viewRenderer->renderWithTemplate('forgot-password', $context);
    }
}