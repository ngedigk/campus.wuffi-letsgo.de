<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\CsrfService;
use App\Services\RegistrationCodeService;

use App\Helpers\ViewRenderer;
use App\Helpers\Redirect;

use App\Exceptions\CsrfException;
use App\Exceptions\RegistrationCodeException;
use PDOException;

class AdminRegistrationCodesController
{
    public function __construct(
        protected RegistrationCodeService $registrationCodeService,
        protected CourseService $courseService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService,
        private CsrfService $csrfService
    ) {}

    public function render(): void
    {
        $context = $this->adminContextService->buildContext(
            $this->authService->currentUser()
        );

        $context['additionalJs'][] = [
            'src' => '/assets/js/admin/registration-codes.js',
            'type' => 'module'
        ];

        $viewData = array_merge(
            $context,
            [
                'activePage' => 'registration-codes',
                'breadcrumb' => [
                    [
                        'url' => '',
                        'title' => 'Registration Codes'
                    ],
                ],
                'registrationCodes' => $this->registrationCodeService->getAll(),
                'allCourses' => $this->courseService->getAll(),
                'pageTitle' => 'Registration Codes'
            ]
        );

        $this->viewRenderer->renderWithAdminTemplate('admin/registration-codes', $viewData);
    }

    public function createRegistrationCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if (!$this->authService->isAdmin()) {
            $_SESSION['error'] = 'Nicht autorisiert.';
            Redirect::to('/');
        }

        if ($code === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie einen Registrierungscode an.';
            Redirect::to('/admin/registration-codes');
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $_SESSION['admin_error'] = 'Ungültiger CSRF-Token.';
            Redirect::to('/admin/registration-codes');
        }

        try {
            $this->registrationCodeService->create($code, $courseIds);
            $_SESSION['admin_success'] = 'Registrierungscode erstellt.';
        } catch (RegistrationCodeException $e) {
            $_SESSION['admin_error'] = $e->getMessage();
            Redirect::to('/admin/registration-codes');
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Ein Fehler ist beim Erstellen des Registrierungscode aufgetreten.';
            Redirect::to('/admin/registration-codes');
        }
        
        Redirect::to('/admin/registration-codes');
    }

    public function updateRegistrationCode(string $registrationCodeId): void
    {
        $registrationCode = trim($_POST['code'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if (!$this->authService->isAdmin()) {
            $_SESSION['error'] = 'Nicht autorisiert.';
            Redirect::to('/');
        }

        if (empty($registrationCode)) {
            $_SESSION['admin_error'] = 'Bitte geben Sie einen Registrierungscode an.';
            Redirect::to('/admin/registration-codes');
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $_SESSION['admin_error'] = 'Ungültiger CSRF-Token.';
            Redirect::to('/admin/registration-codes');
        }

        try {
            $this->registrationCodeService->update((int)$registrationCodeId, $registrationCode);

            $this->registrationCodeService->removeAllCourses((int)$registrationCodeId);

            if (empty($courseIds)) {
                $_SESSION['admin_success'] = 'Kurszuweisung entfernt.';
                Redirect::to('/admin/registration-codes');
            }

            $this->registrationCodeService->addCourses((int)$registrationCodeId, $courseIds);

            $_SESSION['admin_success'] = 'Kurszuweisung aktualisiert.';
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Ein Fehler ist beim Aktualisieren des Registrierungscode aufgetreten.';
            Redirect::to('/admin/registration-codes');
        }
        
        Redirect::to('/admin/registration-codes');
    }

    public function deleteRegistrationCode(string $registrationCodeId): void
    {
        if (!$this->authService->isAdmin()) {
            $_SESSION['error'] = 'Nicht autorisiert.';
            Redirect::to('/');
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $_SESSION['admin_error'] = 'Ungültiger CSRF-Token.';
            Redirect::to('/admin/registration-codes');
        }

        try {
            $this->registrationCodeService->delete((int)$registrationCodeId);
            $_SESSION['admin_success'] = 'Registrierungscode gelöscht.';
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Ein Fehler ist beim Löschen des Registrierungscode aufgetreten.';
            Redirect::to('/admin/registration-codes');
        }
        
        Redirect::to('/admin/registration-codes');
    }
}