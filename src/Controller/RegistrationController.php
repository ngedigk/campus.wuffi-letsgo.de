<?php

namespace App\Controller;

use App\Services\RegistrationService;
use App\Services\CsrfService;
use App\Services\EmailVerificationService;

use App\Helpers\ViewRenderer;

use \Exception;
use \Throwable;

class RegistrationController
{
    public function __construct(
        private RegistrationService $registrationService,
        private CsrfService $csrfService,
        private EmailVerificationService $emailVerificationService,
        private ViewRenderer $viewRenderer,
    ) {}

    public function index(): void
    {
        $this->renderForm([
            'error' => '',
            'success' => '',
            'email' => '',
            'name' => '',
            'registrationCode' => ''
        ]);
    }

    public function verify(): void
    {
        $token = $_GET['token'] ?? '';
        $result = $this->emailVerificationService->verify($token);

        $pageTitle = 'E-Mail Adresse bestätigt';

        if (!$result['success']) {
            $pageTitle = 'E-Mail Adresse nicht bestätigt';
            $_SESSION['verify_error'] = $result['error'] ?? 'Ungültiger oder abgelaufener Token.';
        }

        $headline = $pageTitle;

        $this->viewRenderer->renderWithTemplate('email-verified', [
            'pageTitle' => $pageTitle,
            'headline' => $headline,
            'isLoggedIn' => false,
            'error' => $_SESSION['verify_error'] ?? ''
        ]);

        unset($_SESSION['verify_error']);
    }

    public function register(): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->renderForm([
                'error' => $e->getMessage(),
                'success' => '',
                'email' => $_POST['email'] ?? '',
                'name' => $_POST['name'] ?? '',
                'registrationCode' => $_POST['registration_code'] ?? '',
            ]);
            return;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $name = strtolower(trim($_POST['name'] ?? ''));
        $registrationCode = trim($_POST['registration_code'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->renderForm([
                'error' => 'Ungültige E-Mail Adresse.',
                'success' => '',
                'email' => $email,
                'name' => $name,
                'registrationCode' => $registrationCode
            ]);
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->renderForm([
                'error' => 'Passwörter stimmen nicht überein.',
                'success' => '',
                'email' => $email,
                'name' => $name,
                'registrationCode' => $registrationCode
            ]);
            return;
        }        

        try {
            $this->registrationService->register($email, $password, $registrationCode, $name);
            
            $this->renderForm([
                'error' => '',
                'success' => 'Registrierung erfolgreich. Überprüfen Sie Ihre E-Mails.',
                'email' => '',
                'name' => '',
                'registrationCode' => '',
            ]);

        } catch (Throwable $e) {
            error_log($e);
            $this->renderForm([
                'error' => 'Bei der Erstellung des Accounts ist ein Problem aufgetreten. Informieren Sie den Anbieter und versuchen Sie es später nochmal.',
                'success' => '',
                'email' => $email,
                'name' => $name,
                'registrationCode' => $registrationCode,
            ]);
        }
    }

    private function renderForm(array $context): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Registrierung';
        $context['additionalCss'] = ['/assets/css/password-meter.css', '/assets/css/register.css'];
        $context['additionalJs'] = [
            ['src' => '/assets/js/password-toggle.js'],
            ['src' => '/assets/js/password-meter.js']
        ];
        $context['isLoggedIn'] = false;

        $this->viewRenderer->renderWithTemplate('register-form', $context);
    }
}