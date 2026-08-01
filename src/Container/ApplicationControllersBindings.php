<?php

namespace App\Container;

use App\Controller\AuthController;
use App\Controller\CourseController;
use App\Controller\DashboardController;
use App\Controller\ForgotPasswordController;
use App\Controller\HomeController;
use App\Controller\ProfileController;
use App\Controller\RegistrationController;
use App\Controller\ResetPasswordController;

use App\Repositories\PasswordResetsRepository;
use App\Repositories\UserRepository;

use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\CsrfService;
use App\Services\UserService;
use App\Services\DashboardService;
use App\Services\RedeemService;
use App\Services\RegistrationService;
use App\Services\EmailVerificationService;
use App\Services\MailerService;
use App\Services\CourseSidebarBuilderService;
use App\Services\ProgressService;
use App\Services\QuizService;

use App\Helpers\ViewRenderer;

trait ApplicationControllersBindings
{
    private function registerApplicationControllers(): void
    {
        $this->set(DashboardController::class, fn($c) => new DashboardController(
            $c->get(DashboardService::class),
            $c->get(ViewRenderer::class),
            $c->get(RedeemService::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(AuthController::class, fn($c) => new AuthController(
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(HomeController::class, fn($c) => new HomeController(
            $c->get(DashboardController::class),
            $c->get(AuthController::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(RegistrationController::class, fn($c) => new RegistrationController(
            $c->get(RegistrationService::class),
            $c->get(CsrfService::class),
            $c->get(EmailVerificationService::class),
            $c->get(ViewRenderer::class),
            $c->get(MailerService::class)
        ));
        $this->set(ForgotPasswordController::class, fn($c) => new ForgotPasswordController(
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UserRepository::class),
            $c->get(PasswordResetsRepository::class),
            $c->get(MailerService::class),
            $c->get(ViewRenderer::class)
        ));
        $this->set(ResetPasswordController::class, fn($c) => new ResetPasswordController(
            $c->get(PasswordResetsRepository::class),
            $c->get(UserService::class),
            $c->get(CsrfService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class)
        ));
        $this->set(CourseController::class, fn($c) => new CourseController(
            $c->get(CourseService::class),
            $c->get(CourseSidebarBuilderService::class),
            $c->get(ProgressService::class),
            $c->get(QuizService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class)
        ));
        $this->set(ProfileController::class, fn($c) => new ProfileController(
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            $c->get(UserService::class),
            $c->get(ViewRenderer::class)
        ));
    }
}
