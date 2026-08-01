<?php

namespace App\Controller\Admin;

use Exception;

class AdminAccessCodesController extends AdminPageController
{
    public function render(array $context): void
    {
        $context['additionalJs'][] = [
            'src' => '/assets/js/admin/access-codes.js',
            'type' => 'module'
        ];

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
                throw new Exception('Nicht unterstützte Admin-Aktion.');
        }
    }

    private function handleCreateAccessCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') {
            throw new Exception('Bitte geben Sie sowohl einen Access Code als auch einen Kurs an.');
        }

        if ($this->accessCodeRepository->existsByCode($code)) {
            throw new Exception('Dieser Access Code existiert bereits.');
        }

        $this->accessCodeRepository->create($code, $courseId);
        $_SESSION['admin_success'] = 'Access Code erstellt.';
    }

    private function handleUpdateAccessCode(): void
    {
        $accessCodeId = trim($_POST['access_code_id'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') {
            throw new Exception('Bitte geben Sie sowohl einen Access Code als auch einen Kurs an.');
        }

        $this->accessCodeRepository->update($accessCodeId, $code, $courseId);
        $_SESSION['admin_success'] = 'Access Code aktualisiert.';
    }

    private function handleDeleteAccessCode(): void
    {
        $accessCodeId = trim($_POST['access_code_id'] ?? '');
        $this->accessCodeRepository->delete($accessCodeId);
        $_SESSION['admin_success'] = 'Access Code gelöscht und Benutzerzugriff entfernt.';
    }
}