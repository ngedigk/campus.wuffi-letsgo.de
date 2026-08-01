<?php

namespace App\Controller;

use App\Services\RegistrationService;
use App\Services\CsrfService;
use App\Services\EmailVerificationService;
use App\Services\MailerService;
use App\Helpers\ViewRenderer;
use Exception;
use Throwable;

class RegistrationController
{
    public function __construct(
        private RegistrationService $registrationService,
        private CsrfService $csrfService,
        private EmailVerificationService $emailVerificationService,
        private ViewRenderer $viewRenderer,
        private MailerService $mailerService
    ) {}

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegistration();
            return;
        }

        $this->renderForm([
            'error' => '',
            'success' => '',
            'email' => '',
            'registrationCode' => ''
        ]);
    }

    public function verify(string $token): void
    {
        $result = $this->emailVerificationService->verify($token);

        if ($result['success']) {
            $this->viewRenderer->renderWithTemplate('email-verified', [
                'pageTitle' => 'Email Verified',
                'isLoggedIn' => false
            ]);
        } else {
            $_SESSION['verify_error'] = $result['message'] ?? 'Ungültiger oder abgelaufener Token.';
            header('Location: index.php');
            exit;
        }
    }

    private function handleRegistration(): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->renderForm([
                'error' => $e->getMessage(),
                'success' => '',
                'email' => $_POST['email'] ?? '',
                'registrationCode' => $_POST['registration_code'] ?? '',
            ]);
            return;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $registrationCode = trim($_POST['registration_code'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->renderForm(['error' => 'Ungültige E-Mail Adresse.', 'success' => '', 'email' => $email, 'registrationCode' => $registrationCode]);
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->renderForm(['error' => 'Passwörter stimmen nicht überein.', 'success' => '', 'email' => $email, 'registrationCode' => $registrationCode]);
            return;
        }        

        try {
            $result = $this->registrationService->register($email, $password, $registrationCode);
            
            $this->sendVerificationEmail($email, $result['token']);

            $this->renderForm([
                'error' => '',
                'success' => 'Registrierung erfolgreich. Überprüfen Sie Ihre E-Mails.',
                'email' => '',
                'registrationCode' => '',
            ]);

        } catch (Throwable $e) {
            error_log($e);
            $this->renderForm([
                'error' => 'Bei der Erstellung des Accounts ist ein Problem aufgetreten. Informieren Sie den Anbieter und versuchen Sie es später nochmal.',
                'success' => '',
                'email' => $email,
                'registrationCode' => $registrationCode,
            ]);
        }
    }

    private function renderForm(array $context): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Registrierung';
        $context['additionalCss'] = ['/assets/css/register.css'];
        $context['additionalJs'] = ['/assets/js/password-meter.js'];
        $context['isLoggedIn'] = false;

        $this->viewRenderer->renderWithTemplate('register-form', $context);
    }

    private function sendVerificationEmail(string $email, string $token): void
    {
        $link = SITE_URL . "/register.php?action=verify&token=" . urlencode($token);

        $htmlBody = "<h1>Account bestätigen</h1><p>Klicken Sie auf den unteren Link:</p><a href='" . htmlspecialchars($link) . "'>" . htmlspecialchars($link) . "</a>";

        try {
            $this->mailerService->send($email, 'Bestätigen Sie Ihre E-Mail', $htmlBody);
        } catch (\Exception $e) {
            error_log("Email verification failed: " . $e->getMessage());
        }
    }
}