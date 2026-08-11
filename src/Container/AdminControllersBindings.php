<?php

namespace App\Container;

use App\Controller\Admin\AdminAccessCodesController;
use App\Controller\Admin\AdminAudioAssetsController;
use App\Controller\Admin\AdminCoursesController;
use App\Controller\Admin\AdminDashboardController;
use App\Controller\Admin\AdminRegistrationCodesController;
use App\Controller\Admin\AdminUsersController;

use App\Contracts\Services\UserServiceInterface;

use App\Services\AdminCourseManagementService;
use App\Services\AdminContextService;
use App\Services\AssetsService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\RegistrationCodeService;
use App\Services\SlideService;
use App\Services\AccessCodeService;

use App\Helpers\ViewRenderer;
use App\Services\CsrfService;
use App\Services\RegistrationService;

trait AdminControllersBindings
{
    private function registerAdminControllers(): void
    {
        $this->set(AdminDashboardController::class, fn($c) => new AdminDashboardController(
            $c->get(AccessCodeService::class),
            $c->get(UserServiceInterface::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class)
        ));
        $this->set(AdminCoursesController::class, fn($c) => new AdminCoursesController(
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AssetsService::class),
            $c->get(AdminContextService::class),
            $c->get(AdminCourseManagementService::class)
        ));
        $this->set(AdminAccessCodesController::class, fn($c) => new AdminAccessCodesController(
            $c->get(AccessCodeService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(AdminUsersController::class, fn($c) => new AdminUsersController(
            $c->get(UserServiceInterface::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(RegistrationService::class),
            $c->get(AdminContextService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(AdminRegistrationCodesController::class, fn($c) => new AdminRegistrationCodesController(
            $c->get(RegistrationCodeService::class),
            $c->get(CourseService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(AdminAudioAssetsController::class, fn($c) => new AdminAudioAssetsController(
            $c->get(SlideService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class),
            $c->get(AssetsService::class),
            $c->get(CsrfService::class)
        ));
    }
}
