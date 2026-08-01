<?php

namespace App\Controller\Admin;

use Exception;

class AdminRegistrationCodesController extends AdminPageController
{
    public function render(array $context): void
    {
        $context['additionalJs'][] = '/assets/js/admin/registration-codes.js';

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
            case 'update_courses_to_registration_code_assignment':
                $this->handleUpdateCoursesToRegistrationCode();
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
            throw new Exception('Please provide a registration code.');
        }

        $this->registrationCodeService->create($code, $courseIds);
        $_SESSION['admin_success'] = 'Registration code created.';
    }

    private function handleUpdateCoursesToRegistrationCode(): void
    {
        $registrationCodeId = (int)trim($_POST['registration_code_id'] ?? '');
        $courseIds = $_POST['course_ids'] ?? [];

        if ($registrationCodeId === 0) {
            throw new Exception('Please provide a valid registration code ID.');
        }

        $this->registrationCodeService->removeAllCourses($registrationCodeId);

        if (empty($courseIds)) {
            $_SESSION['admin_success'] = 'Courses assignment removed.';
            return;
        }

        $this->registrationCodeService->addCourses($registrationCodeId, $courseIds);

        $_SESSION['admin_success'] = 'Courses assignment updated.';
    }

    private function handleDeleteRegistrationCode(): void
    {
        $registrationCodeId = (int)trim($_POST['registration_code_id'] ?? '');

        if ($registrationCodeId === 0) {
            throw new Exception('Please provide a valid registration code ID.');
        }

        $this->registrationCodeService->delete($registrationCodeId);
        $_SESSION['admin_success'] = 'Registration code deleted.';
    }
}