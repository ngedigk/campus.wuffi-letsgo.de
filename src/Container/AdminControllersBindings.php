<?php

namespace App\Container;

use App\Controller\Admin\AdminAccessCodesController;
use App\Controller\Admin\AdminCoursesController;
use App\Controller\Admin\AdminDashboardController;
use App\Controller\Admin\AdminRegistrationCodesController;
use App\Controller\Admin\AdminUsersController;
use App\Controller\AdminController;

use App\Repositories\AccessCodeRepository;

use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\CsrfService;
use App\Services\ModuleService;
use App\Services\RegistrationCodeService;
use App\Services\SlideService;
use App\Services\UuidService;
use App\Services\UserService;

use App\Helpers\ViewRenderer;


trait AdminControllersBindings
{
    private function registerAdminControllers(): void
    {
        $this->set(AdminDashboardController::class, fn($c) => new AdminDashboardController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UuidService::class),
            $c->get(RegistrationCodeService::class)
        ));
        $this->set(AdminCoursesController::class, fn($c) => new AdminCoursesController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UuidService::class),
            $c->get(RegistrationCodeService::class)
        ));
        $this->set(AdminAccessCodesController::class, fn($c) => new AdminAccessCodesController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UuidService::class),
            $c->get(RegistrationCodeService::class)
        ));
        $this->set(AdminUsersController::class, fn($c) => new AdminUsersController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UuidService::class),
            $c->get(RegistrationCodeService::class)
        ));
        $this->set(AdminRegistrationCodesController::class, fn($c) => new AdminRegistrationCodesController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UuidService::class),
            $c->get(RegistrationCodeService::class)
        ));
        $this->set(AdminController::class, fn($c) => new AdminController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UuidService::class),
            $c->get(RegistrationCodeService::class),
            $c->get(AdminDashboardController::class),
            $c->get(AdminCoursesController::class),
            $c->get(AdminAccessCodesController::class),
            $c->get(AdminUsersController::class),
            $c->get(AdminRegistrationCodesController::class)
        ));
    }
}
