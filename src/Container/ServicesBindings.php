<?php

namespace App\Container;

use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\CourseSidebarBuilderService;
use App\Services\CsrfService;
use App\Services\DashboardService;
use App\Services\EmailVerificationService;
use App\Services\MailerService;
use App\Services\ModuleService;
use App\Services\ProgressService;
use App\Services\QuizService;
use App\Services\RedeemService;
use App\Services\RegistrationCodeService;
use App\Services\RegistrationService;
use App\Services\SlideService;
use App\Services\UserService;
use App\Services\UuidService;
use App\Services\QuizQuestionService;

use App\Repositories\AccessCodeRepository;
use App\Repositories\AuthRepository;
use App\Repositories\CourseRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\SlideRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\RegistrationCodeRepository;
use App\Repositories\UserCourseRepository;
use App\Repositories\UserRepository;

use PDO;

trait ServicesBindings
{
    private function registerServices(): void
    {
        $this->set(CsrfService::class, fn() => new CsrfService());
        $this->set(UuidService::class, fn() => new UuidService());
        $this->set(MailerService::class, fn() => new MailerService());
        $this->set(UserService::class, fn($c) => new UserService($c->get(UserRepository::class)));
        $this->set(AuthService::class, fn($c) => new AuthService($c->get(UserService::class), $c->get(AuthRepository::class)));
        $this->set(CourseService::class, fn($c) => new CourseService(
            $c->get(CourseRepository::class),
            $c->get(ModuleRepository::class),
            $c->get(SlideRepository::class),
            $c->get(QuizQuestionRepository::class)
        ));
        $this->set(ModuleService::class, fn($c) => new ModuleService($c->get(ModuleRepository::class)));
        $this->set(SlideService::class, fn($c) => new SlideService($c->get(SlideRepository::class)));
        $this->set(QuizService::class, fn($c) => new QuizService($c->get(QuizQuestionRepository::class)));
        $this->set(QuizQuestionService::class, fn($c) => new QuizQuestionService($c->get(QuizQuestionRepository::class)));
        $this->set(ProgressService::class, fn($c) => new ProgressService($c->get(ProgressRepository::class)));
        $this->set(RedeemService::class, fn($c) => new RedeemService(
            $c->get(PDO::class),
            $c->get(AccessCodeRepository::class),
            $c->get(UserCourseRepository::class)
        ));
        $this->set(DashboardService::class, fn($c) => new DashboardService(
            $c->get(CourseService::class),
            $c->get(ProgressService::class)
        ));
        $this->set(RegistrationService::class, fn($c) => new RegistrationService(
            $c->get(PDO::class),
            $c->get(UserRepository::class),
            $c->get(EmailVerificationRepository::class),
            $c->get(RegistrationCodeRepository::class),
            $c->get(AccessCodeRepository::class),
            $c->get(UuidService::class)
        ));
        $this->set(CourseSidebarBuilderService::class, fn($c) => new CourseSidebarBuilderService(
            $c->get(CourseService::class)
        ));
        $this->set(EmailVerificationService::class, fn($c) => new EmailVerificationService(
            $c->get(PDO::class),
            $c->get(EmailVerificationRepository::class),
            $c->get(UserRepository::class)
        ));
        $this->set(RegistrationCodeService::class, fn($c) => new RegistrationCodeService(
            $c->get(RegistrationCodeRepository::class),
            $c->get(CourseRepository::class)
        ));
    }
}
