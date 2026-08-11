<?php

namespace App\Controller\Admin;

use App\Services\AccessCodeService;
use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\CsrfService;

use App\Helpers\ViewRenderer;
use App\Helpers\Redirect;

use App\Exceptions\AccessCodeException;
use App\Exceptions\CsrfException;
use PDOException;

class AdminAccessCodesController
{
    public function __construct(
        protected AccessCodeService $accessCodeService,
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

        if (!$this->authService->isAdmin()) {
            $_SESSION['error'] = 'Nicht autorisiert.';
            Redirect::to('/');
        }

        if ($code === '' || $courseId === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie sowohl einen Access Code als auch einen Kurs an.';
            Redirect::to('/admin/access-codes');
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $_SESSION['admin_error'] = 'Ungültiger CSRF-Token.';
            Redirect::to('/admin/access-codes');
        }

        if ($this->accessCodeService->existsByCode($code)) {
            $_SESSION['admin_error'] = 'Dieser Access Code existiert bereits.';
            Redirect::to('/admin/access-codes');
        }

        try {
            $this->accessCodeService->create($code, $courseId);
            $_SESSION['admin_success'] = 'Access Code erstellt.';
        } catch (AccessCodeException $e) {
            $_SESSION['admin_error'] = $e->getMessage();
            Redirect::to('/admin/access-codes');
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Ein Fehler ist beim Erstellen des Access Codes aufgetreten.';
            Redirect::to('/admin/access-codes');
        }
        
        Redirect::to('/admin/access-codes');
    }

    public function updateAccessCode(string $accessCodeId): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if (!$this->authService->isAdmin()) {
            $_SESSION['error'] = 'Nicht autorisiert.';
            Redirect::to('/');
        }

        if ($code === '' || $courseId === '') {
            $_SESSION['admin_error'] = 'Bitte geben Sie sowohl einen Access Code als auch einen Kurs an.';
            Redirect::to('/admin/access-codes');
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $_SESSION['admin_error'] = 'Ungültiger CSRF-Token.';
            Redirect::to('/admin/access-codes');
        }

        try {
            $this->accessCodeService->update((int)$accessCodeId, $code, $courseId);
            $_SESSION['admin_success'] = 'Access Code aktualisiert.';
        } catch (AccessCodeException $e) {
            $_SESSION['admin_error'] = $e->getMessage();
            Redirect::to('/admin/access-codes');
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Ein Fehler ist beim Aktualisieren des Access Codes aufgetreten.';
            Redirect::to('/admin/access-codes');
        }

        Redirect::to('/admin/access-codes');
    }

    public function deleteAccessCode(string $accessCodeId): void
    {
        if (!$this->authService->isAdmin()) {
            $_SESSION['error'] = 'Nicht autorisiert.';
            Redirect::to('/');
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $_SESSION['admin_error'] = 'Ungültiger CSRF-Token.';
            Redirect::to('/admin/access-codes');
        }

        try {
            $this->accessCodeService->delete((int)$accessCodeId);
            $_SESSION['admin_success'] = 'Access Code gelöscht und Benutzerzugriff entfernt.';
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = 'Ein Fehler ist beim Löschen des Access Codes aufgetreten.';
            Redirect::to('/admin/access-codes');
        }

        Redirect::to('/admin/access-codes');
    }
}