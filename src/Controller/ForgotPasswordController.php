<?php

class ForgotPasswordController
{
    public function __construct(
        private AuthService $authService,
        private CsrfService $csrfService,
        private UserRepository $userRepository,
        private PasswordResetsRepository $passwordResetsRepository,
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
        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->passwordResetsRepository->recordReset($user->id, $token);
            $this->sendResetEmail($email, $token);
        }

        $this->renderForm(['message' => 'If the email exists, a reset link was sent.']);
    }

    private function sendResetEmail(string $email, string $token): void
    {
        $link = SITE_URL . "/reset-password.php?token=" . urlencode($token);
        $htmlBody = "<p>Reset your password:</p><a href='" . htmlspecialchars($link) . "'>" . htmlspecialchars($link) . "</a>";

        try {
            $this->mailerService->send($email, 'Password Reset', $htmlBody);
        } catch (\Exception $e) {
            error_log("Reset email failed: " . $e->getMessage());
        }
    }

    private function renderForm(array $context): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Forgot Password';
        $context['isLoggedIn'] = $this->authService->isLoggedIn();

        $this->viewRenderer->renderWithTemplate('forgot-password', $context);
    }
}
