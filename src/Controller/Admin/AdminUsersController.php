<?php

namespace App\Controller\Admin;

use App\Contracts\Services\UserServiceInterface;

use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\RegistrationService;

use App\Helpers\ViewRenderer;
use App\Helpers\Redirect;

use App\Exceptions\UserNotFoundException;

use \Exception;

class AdminUsersController
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected RegistrationService $registrationService,
        protected AdminContextService $adminContextService
    ) {}

    public function render(): void
    {
        $context = $this->adminContextService->buildContext(
            $this->authService->currentUser()
        );

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

    public function grantAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        
        if ($email === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie eine E-Mail-Adresse an.';
            Redirect::to('/admin/users');
        }

        try {
            $this->userService->grantAdmin($email);
        } catch (UserNotFoundException $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        $_SESSION['admin_success'] = 'Admin-Berechtigung erteilt.';

        Redirect::to('/admin/users');
    }

    public function revokeAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        
        if ($email === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie eine E-Mail-Adresse an.';
            Redirect::to('/admin/users');
        }

        if ($email === strtolower($this->authService->currentUser()->email)) {
            $_SESSION['admin_error'] = 'Sie können Ihre eigene Admin-Berechtigung nicht entfernen.';
        } else {
            $this->userService->removeAdmin($email);
            $_SESSION['admin_success'] = 'Admin-Berechtigung entfernt.';
        }

        Redirect::to('/admin/users');
    }

    public function manuallyVerify(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie eine E-Mail-Adresse an.';
            Redirect::to('/admin/users');
        }

        try {
            $this->userService->verify($email);
            $_SESSION['admin_success'] = 'Benutzer manuell verifiziert.';
        } catch (UserNotFoundException $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        Redirect::to('/admin/users');
    }

    public function resendVerificationEmail(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie eine E-Mail-Adresse an.';
            Redirect::to('/admin/users');
        }

        try {
            $this->registrationService->resendVerificationEmail($email);
            $_SESSION['admin_success'] = 'Verifizierungsmail wurde gesendet.';
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        Redirect::to('/admin/users');
    }
}