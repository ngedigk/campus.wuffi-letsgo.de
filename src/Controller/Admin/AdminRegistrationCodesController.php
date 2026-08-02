<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\RegistrationCodeService;

use App\Helpers\ViewRenderer;

use \Exception;

class AdminRegistrationCodesController extends AdminPageController
{
    public function __construct(
        protected RegistrationCodeService $registrationCodeService,
        protected CourseService $courseService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService
    ) {
        parent::__construct($adminContextService, $authService);
    }

    public function render(array $context): void
    {
        $context['additionalJs'][] = [
            'src' => '/assets/js/admin/registration-codes.js',
            'type' => 'module'
        ];

        $viewData = [
            ...$context,
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
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/registration-codes', $viewData);
    }

    public function handlePost(string $action): void
    {
        switch ($action) {
            case 'create_registration_code':
                $this->handleCreateRegistrationCode();
                break;
            case 'update_registration_code':
                $this->handleUpdateRegistrationCode();
                break;
            case 'delete_registration_code':
                $this->handleDeleteRegistrationCode();
                break;
            default:
                throw new Exception('Unsupported admin action.');
        }
    }

    private function handleCreateRegistrationCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if ($code === '') {
            throw new Exception('Bitte geben Sie einen Registrierungscode an.');
        }

        $this->registrationCodeService->create($code, $courseIds);
        $_SESSION['admin_success'] = 'Registrierungscode erstellt.';
    }

    private function handleUpdateRegistrationCode(): void
    {
        $registrationCodeId = (int)trim($_POST['registration_code_id'] ?? '');
        $registrationCode = trim($_POST['code'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if (empty($registrationCode)) {
            throw new Exception('Bitte geben Sie einen Registrierungscode an.');
        }

        $this->registrationCodeService->update($registrationCodeId, $registrationCode);

        $this->registrationCodeService->removeAllCourses($registrationCodeId);

        if (empty($courseIds)) {
            $_SESSION['admin_success'] = 'Kurszuweisung entfernt.';
            return;
        }

        $this->registrationCodeService->addCourses($registrationCodeId, $courseIds);

        $_SESSION['admin_success'] = 'Kurszuweisung aktualisiert.';
    }

    private function handleDeleteRegistrationCode(): void
    {
        $registrationCodeId = (int)trim($_POST['registration_code_id'] ?? '');

        if ($registrationCodeId === 0) {
            throw new Exception('Bitte geben Sie eine gültige Registrierungscode-ID an.');
        }

        $this->registrationCodeService->delete($registrationCodeId);
        $_SESSION['admin_success'] = 'Registrierungscode gelöscht.';
    }
}