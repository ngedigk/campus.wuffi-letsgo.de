<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\RegistrationService;
use App\Services\UserService;

use App\Helpers\ViewRenderer;
use \Exception;

class AdminUsersController extends AdminPageController
{
    public function __construct(
        protected UserService $userService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected RegistrationService $registrationService,
        protected AdminContextService $adminContextService
    ) {
        parent::__construct($adminContextService, $authService);
    }

    public function render(array $context): void
    {
        $viewData = array_merge(
            $context,
            [
                'activePage' => 'users',
                'breadcrumb' => [
                    [
                        'url' => '',
                        'title' => 'Benutzer'
                    ],
                ],
                'allUsers' => $this->userService->getAll(),
                'pageTitle' => 'Benutzer'
            ]
        );

        $this->viewRenderer->renderWithAdminTemplate('admin/users', $viewData);
    }

    public function handlePost(string $action): void
    {
        switch ($action) {
            case 'grant_admin':
                $this->handleGrantAdmin();
                break;
            case 'revoke_admin':
                $this->handleRevokeAdmin();
                break;
            case 'resend_verification_email':
                $this->handleResendVerificationEmail();
                break;
            case 'manually_verify':
                $this->handleManuallyVerify();
                break;
            default:
                throw new Exception("Nicht unterstützte Admin-Aktion \"$action\".");
        }
    }

    private function handleGrantAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') {
            throw new Exception('Bitte geben Sie eine E-Mail-Adresse an.');
        }

        $this->userService->grantAdmin($email);
        $_SESSION['admin_success'] = 'Admin-Berechtigung erteilt.';
    }

    private function handleRevokeAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') {
            throw new Exception('Bitte geben Sie eine E-Mail-Adresse an.');
        }

        if ($email === strtolower($this->authService->currentUser()->email)) {
            throw new Exception("Sie können Ihre eigene Admin-Berechtigung nicht entfernen.");
        }

        $this->userService->removeAdmin($email);
        $_SESSION['admin_success'] = 'Admin-Berechtigung entfernt.';
    }

    private function handleManuallyVerify(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            throw new Exception('Bitte geben Sie eine E-Mail-Adresse an.');
        }

        $this->userService->verify($email);
        $_SESSION['admin_success'] = 'Benutzer manuell verifiziert.';
    }

    private function handleResendVerificationEmail(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            throw new Exception('Bitte geben Sie eine E-Mail-Adresse an.');
        }

        $this->registrationService->resendVerificationEmail($email);

        $_SESSION['admin_success'] = 'Verifizierungsmail wurde gesendet.';
    }
}