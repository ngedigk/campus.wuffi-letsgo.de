<?php

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

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->userService->setPassword($userUuid, $hash);
            $this->passwordResetsRepository->deleteRecord($userUuid);

            $this->renderForm(['success' => 'Password updated successfully.'], $userUuid);
        } catch (Throwable $e) {
            error_log($e);
            $this->renderForm(['error' => 'Failed to update password.'], $userUuid);
        }
    }

    private function renderForm(array $context, string $userUuid): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Reset Password';
        $context['isLoggedIn'] = $this->authService->isLoggedIn();

        $this->viewRenderer->renderWithTemplate('reset-password', $context);
    }
}
