<?php

class AdminAccessCodesController extends AdminPageController
{
    public function render(array $context): void
    {
        $context['additionalJs'][] = '/assets/js/admin/access-codes.js';

        $viewData = [
            ...$context,
            'activePage' => 'access-codes',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title' => 'Access Codes'
                ],
            ],
            'accessCodes' => $this->accessCodeRepository->getAll(),
            'pageTitle' => 'Access Codes'
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/access-codes', $viewData);
    }

    public function handlePost(string $action): void
    {
        switch ($action) {
            case 'create_access_code':
                $this->handleCreateAccessCode();
                break;
            case 'update_access_code':
                $this->handleUpdateAccessCode();
                break;
            case 'delete_access_code':
                $this->handleDeleteAccessCode();
                break;
            default:
                throw new Exception('Unsupported admin action.');
        }
    }

    private function handleCreateAccessCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') {
            throw new Exception('Please provide both an access code and a course.');
        }

        if ($this->accessCodeRepository->existsByCode($code)) {
            throw new Exception('That access code already exists.');
        }

        $this->accessCodeRepository->create($code, $courseId);
        $_SESSION['admin_success'] = 'Access code created.';
    }

    private function handleUpdateAccessCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') {
            throw new Exception('Please provide both an access code and a course.');
        }

        $this->accessCodeRepository->update($code, $courseId);
        $_SESSION['admin_success'] = 'Access code updated.';
    }

    private function handleDeleteAccessCode(): void
    {
        $accessCodeId = trim($_POST['access_code_id'] ?? '');
        $this->accessCodeRepository->delete($accessCodeId);
        $_SESSION['admin_success'] = 'Access code deleted and removed user access.';
    }
}
