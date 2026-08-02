<?php

namespace App\Controller;

use App\Controller\Admin\AdminAccessCodesController;
use App\Controller\Admin\AdminAudioAssetsController;
use App\Controller\Admin\AdminCoursesController;
use App\Controller\Admin\AdminDashboardController;
use App\Controller\Admin\AdminPageController;
use App\Controller\Admin\AdminRegistrationCodesController;
use App\Controller\Admin\AdminUsersController;
use App\Services\AdminContextService;
use App\Services\AuthService;
use Exception;
use Throwable;

class AdminController extends AdminPageController
{
    public function __construct(
        AdminContextService $adminContextService,
        AuthService $authService,
        private AdminDashboardController $dashboardController,
        private AdminCoursesController $coursesController,
        private AdminAccessCodesController $accessCodesController,
        private AdminUsersController $usersController,
        private AdminRegistrationCodesController $registrationCodesController,
        private AdminAudioAssetsController $audioAssetsController,
    ) {
        parent::__construct($adminContextService, $authService);
    }

    public function handle(string $page): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        $user = $this->authService->currentUser();
        $page = $this->validatePage($page);
        $context = $this->buildContext($user);

        switch ($page) {
            case 'dashboard':
                $this->dashboardController->render($context);
                break;
            case 'courses':
                $this->coursesController->render($context);
                break;
            case 'access-codes':
                $this->accessCodesController->render($context);
                break;
            case 'users':
                $this->usersController->render($context);
                break;
            case 'registration-codes':
                $this->registrationCodesController->render($context);
                break;
            case 'audio-assets':
                $this->audioAssetsController->render($context);
                break;
            default:
                $this->dashboardController->render($context);
                break;
        }
    }

    private function handlePost(): void
    {
        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'grant_admin':
                case 'revoke_admin':
                case 'manually_verify':
                    $this->usersController->handlePost($action);
                    break;
                case 'create_course':
                case 'update_course':
                case 'create_module':
                case 'update_module':
                case 'create_slide':
                case 'update_slide':
                case 'create_question':
                case 'update_question':
                case 'delete_question':
                case 'delete_slide':
                case 'delete_module':
                case 'delete_course':
                case 'upload_image':
                case 'delete_image':
                    $this->coursesController->handlePost($action);
                    break;
                case 'create_access_code':
                case 'update_access_code':
                case 'delete_access_code':
                    $this->accessCodesController->handlePost($action);
                    break;
                case 'create_registration_code':
                case 'update_registration_code':
                case 'delete_registration_code':
                    $this->registrationCodesController->handlePost($action);
                    break;
                case 'delete_audio_asset':
                    $this->audioAssetsController->handlePost($action);
                    break;
                default:
                    throw new Exception("Nicht unterstützte Admin-Aktion \"$action\".");
            }
        } catch (Throwable $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }
    }

    private function validatePage(string $page): string
    {
        $validPages = [
            'dashboard',
            'courses',
            'access-codes',
            'users',
            'registration-codes',
            'audio-assets'
        ];
        return in_array($page, $validPages, true) ? $page : 'dashboard';
    }
}