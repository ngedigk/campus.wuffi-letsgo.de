<?php

namespace App\Controller\Admin;

use App\Services\AccessCodeService;
use App\Services\AdminContextService;
use App\Services\AuthService;

use App\Helpers\ViewRenderer;
use App\Helpers\Redirect;

use \Exception;

class AdminAccessCodesController
{
    public function __construct(
        protected AccessCodeService $accessCodeService,
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
            'src' => '/assets/js/admin/access-codes.js',
            'type' => 'module'
        ];

        $viewData = array_merge(
            $context,
            [
                'activePage' => 'access-codes',
                'breadcrumb' => [
                    [
                        'url' => '',
                        'title' => 'Access Codes'
                    ],
                ],
                'accessCodes' => $this->accessCodeService->getAll(),
                'pageTitle' => 'Access Codes'
            ]
        );

        $this->viewRenderer->renderWithAdminTemplate('admin/access-codes', $viewData);
    }

    public function createAccessCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') {
            throw new Exception('Bitte geben Sie sowohl einen Access Code als auch einen Kurs an.');
        }

        if ($this->accessCodeService->existsByCode($code)) {
            throw new Exception('Dieser Access Code existiert bereits.');
        }

        $this->accessCodeService->create($code, $courseId);
        $_SESSION['admin_success'] = 'Access Code erstellt.';
        
        Redirect::to('/admin/access-codes');
    }

    public function updateAccessCode(string $accessCodeId): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') {
            throw new Exception('Bitte geben Sie sowohl einen Access Code als auch einen Kurs an.');
        }

        $this->accessCodeService->update((int)$accessCodeId, $code, $courseId);
        $_SESSION['admin_success'] = 'Access Code aktualisiert.';

        Redirect::to('/admin/access-codes');
    }

    public function deleteAccessCode(string $accessCodeId): void
    {
        $this->accessCodeService->delete((int)$accessCodeId);
        $_SESSION['admin_success'] = 'Access Code gelöscht und Benutzerzugriff entfernt.';
        
        Redirect::to('/admin/access-codes');
    }
}