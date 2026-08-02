<?php

namespace App\Container;

use App\Controller\Admin\AdminAccessCodesController;
use App\Controller\Admin\AdminAudioAssetsController;
use App\Controller\Admin\AdminCoursesController;
use App\Controller\Admin\AdminDashboardController;
use App\Controller\Admin\AdminRegistrationCodesController;
use App\Controller\Admin\AdminUsersController;
use App\Controller\AdminController;

use App\Services\QuestionChoiceService;
use App\Services\AdminContextService;
use App\Services\AssetsService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\ModuleService;
use App\Services\RegistrationCodeService;
use App\Services\SlideService;
use App\Services\UserService;
use App\Services\UuidService;
use App\Services\QuizQuestionService;
use App\Services\AccessCodeService;

use App\Helpers\ViewRenderer;


trait AdminControllersBindings
{
    private function registerAdminControllers(): void
    {
        $this->set(AdminDashboardController::class, fn($c) => new AdminDashboardController(
            $c->get(AccessCodeService::class),
            $c->get(UserService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class)
        ));
        $this->set(AdminCoursesController::class, fn($c) => new AdminCoursesController(
            $c->get(CourseService::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(UuidService::class),
            $c->get(QuizQuestionService::class),
            $c->get(QuestionChoiceService::class),
            $c->get(AdminContextService::class),
            $c->get(AssetsService::class)
        ));
        $this->set(AdminAccessCodesController::class, fn($c) => new AdminAccessCodesController(
            $c->get(AccessCodeService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class)
        ));
        $this->set(AdminUsersController::class, fn($c) => new AdminUsersController(
            $c->get(UserService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class)
        ));
        $this->set(AdminRegistrationCodesController::class, fn($c) => new AdminRegistrationCodesController(
            $c->get(RegistrationCodeService::class),
            $c->get(CourseService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class)
        ));
        $this->set(AdminAudioAssetsController::class, fn($c) => new AdminAudioAssetsController(
            $c->get(SlideService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(AdminContextService::class),
            $c->get(AssetsService::class)
        ));
        $this->set(AdminController::class, fn($c) => new AdminController(
            $c->get(AdminContextService::class),
            $c->get(AuthService::class),
            $c->get(AdminDashboardController::class),
            $c->get(AdminCoursesController::class),
            $c->get(AdminAccessCodesController::class),
            $c->get(AdminUsersController::class),
            $c->get(AdminRegistrationCodesController::class),
            $c->get(AdminAudioAssetsController::class),
        ));
    }
}
