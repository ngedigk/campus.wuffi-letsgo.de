<?php

namespace App\Controller;

use App\Repositories\PasswordResetsRepository;
use App\Services\CsrfService;
use App\Services\UserService;
use App\Services\AuthService;
use App\Helpers\ViewRenderer;
use Exception;
use Throwable;

class ResetPasswordController
{
    public function __construct(
        private PasswordResetsRepository $passwordResetsRepository,
        private UserService $userService,
        private CsrfService $csrfService,
        private ViewRenderer $viewRenderer,
        private AuthService $authService
    ) {}

    public function index(): void
    {
        $token = $_GET['token'] ?? '';
        $userUuid = $this->passwordResetsRepository->getUserUuidByToken($token);

        if (!$userUuid) {
            http_response_code(404);
            echo "Invalid or expired token.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleReset($userUuid);
            return;
        }

        $this->renderForm([], $userUuid);
    }

    private function handleReset(string $userUuid): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
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
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->userService->setPassword($userUuid, $hash);
            $this->passwordResetsRepository->deleteRecord($userUuid);

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
        $context['additionalCss'] = ['/assets/css/register.css'];
        $context['additionalJs'] = [['src' => '/assets/js/password-meter.js']];

        $this->viewRenderer->renderWithTemplate('reset-password', $context);
    }
}