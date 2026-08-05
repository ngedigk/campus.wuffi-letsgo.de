<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\RegistrationCodeService;

use App\Helpers\ViewRenderer;
use App\Helpers\Redirect;

use App\Exceptions\RegistrationCodeException;

use \Exception;

class AdminRegistrationCodesController
{
    public function __construct(
        protected RegistrationCodeService $registrationCodeService,
        protected CourseService $courseService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService
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

        if ($code === '') {
            throw new Exception('Bitte geben Sie einen Registrierungscode an.');
        }

        try {
            $this->registrationCodeService->create($code, $courseIds);
            $_SESSION['admin_success'] = 'Registrierungscode erstellt.';
        } catch (RegistrationCodeException $e) {
            $_SESSION['admin_error'] = 'Registrierungscode existiert bereits.';
        }
        
        
        Redirect::to('/admin/registration-codes');
    }

    public function updateRegistrationCode(string $registrationCodeId): void
    {
        $registrationCode = trim($_POST['code'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if (empty($registrationCode)) {
            throw new Exception('Bitte geben Sie einen Registrierungscode an.');
        }

        $this->registrationCodeService->update((int)$registrationCodeId, $registrationCode);

        $this->registrationCodeService->removeAllCourses((int)$registrationCodeId);

        if (empty($courseIds)) {
            $_SESSION['admin_success'] = 'Kurszuweisung entfernt.';
            return;
        }

        $this->registrationCodeService->addCourses((int)$registrationCodeId, $courseIds);

        $_SESSION['admin_success'] = 'Kurszuweisung aktualisiert.';
        
        Redirect::to('/admin/registration-codes');
    }

    public function deleteRegistrationCode(string $registrationCodeId): void
    {
        if ((int)$registrationCodeId === 0) {
            throw new Exception('Bitte geben Sie eine gültige Registrierungscode-ID an.');
        }

        $this->registrationCodeService->delete((int)$registrationCodeId);
        $_SESSION['admin_success'] = 'Registrierungscode gelöscht.';
        
        Redirect::to('/admin/registration-codes');
    }
}