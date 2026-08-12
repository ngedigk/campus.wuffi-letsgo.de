<?php

namespace App\Container;

use App\Contracts\Mail\MailerInterface;
use App\Contracts\Database\TransactionManagerInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Contracts\Repositories\AccessCodeRepositoryInterface;
use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Repositories\EmailVerificationRepositoryInterface;
use App\Contracts\Repositories\ModuleRepositoryInterface;
use App\Contracts\Repositories\PasswordResetsRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionChoiceRepositoryInterface;
use App\Contracts\Repositories\QuizQuestionRepositoryInterface;
use App\Contracts\Repositories\RegistrationCodeRepositoryInterface;
use App\Contracts\Repositories\SlideRepositoryInterface;
use App\Contracts\Repositories\UserCourseRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

use App\Services\AdminContextService;
use App\Services\AdminCourseManagementService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\CourseSidebarBuilderService;
use App\Services\CsrfService;
use App\Services\DashboardService;
use App\Services\EmailVerificationService;
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
use App\Services\QuestionChoiceService;
use App\Services\AssetsService;
use App\Services\AccessCodeService;
use App\Services\PasswordResetService;
use App\Services\CourseNavigationService;

use Psr\Log\LoggerInterface;
use App\Infrastructure\Logging\AppLogger;

trait ServicesBindings
{
    private function registerServices(): void
    {
        $this->set(CsrfService::class, fn() => new CsrfService());
        $this->set(AdminContextService::class, fn($c) => new AdminContextService(
            $c->get(CourseService::class),
            $c->get(AuthService::class),
            $c->get(CsrfService::class)
        ));
        $this->set(UuidService::class, fn() => new UuidService());
        $this->set(UserServiceInterface::class, fn($c) => new UserService($c->get(UserRepositoryInterface::class)));
        $this->set(AuthService::class, fn($c) => new AuthService($c->get(UserServiceInterface::class), $c->get(AuthRepositoryInterface::class)));
        $this->set(CourseService::class, fn($c) => new CourseService(
            $c->get(UuidService::class),
            $c->get(CourseRepositoryInterface::class),
            $c->get(ModuleService::class)
        ));
        $this->set(ModuleService::class, fn($c) => new ModuleService(
            $c->get(ModuleRepositoryInterface::class),
            $c->get(SlideService::class)
        ));
        $this->set(SlideService::class, fn($c) => new SlideService(
            $c->get(SlideRepositoryInterface::class),
            $c->get(QuizQuestionRepositoryInterface::class)
        ));
        $this->set(QuizService::class, fn($c) => new QuizService($c->get(QuizQuestionRepositoryInterface::class)));
        $this->set(QuizQuestionService::class, fn($c) => new QuizQuestionService(
            $c->get(QuizQuestionRepositoryInterface::class),
            $c->get(QuestionChoiceService::class)
        ));
        $this->set(QuestionChoiceService::class, fn($c) => new QuestionChoiceService(
            $c->get(QuestionChoiceRepositoryInterface::class)
        ));
        $this->set(AdminCourseManagementService::class, fn($c) => new AdminCourseManagementService(
            $c->get(CourseService::class),
            $c->get(ModuleService::class),
            $c->get(SlideService::class),
            $c->get(QuizQuestionService::class),
            $c->get(QuestionChoiceService::class),
            $c->get(AssetsService::class),
            $c->get(TransactionManagerInterface::class)
        ));
        $this->set(ProgressService::class, fn($c) => new ProgressService($c->get(ProgressRepositoryInterface::class)));
        $this->set(AccessCodeService::class, fn($c) => new AccessCodeService($c->get(AccessCodeRepositoryInterface::class)));
        $this->set(RedeemService::class, fn($c) => new RedeemService(
            $c->get(TransactionManagerInterface::class),
            $c->get(AccessCodeRepositoryInterface::class),
            $c->get(UserCourseRepositoryInterface::class)
        ));
        $this->set(DashboardService::class, fn($c) => new DashboardService(
            $c->get(CourseService::class),
            $c->get(ProgressService::class)
        ));
        $this->set(RegistrationService::class, fn($c) => new RegistrationService(
            $c->get(TransactionManagerInterface::class),
            $c->get(MailerInterface::class),
            $c->get(UserRepositoryInterface::class),
            $c->get(EmailVerificationRepositoryInterface::class),
            $c->get(RegistrationCodeRepositoryInterface::class),
            $c->get(AccessCodeRepositoryInterface::class),
            $c->get(UuidService::class),
            $c->get(LoggerInterface::class)
        ));
        $this->set(CourseNavigationService::class, fn($c) => new CourseNavigationService(
            $c->get(CourseService::class)
        ));
        $this->set(CourseSidebarBuilderService::class, fn($c) => new CourseSidebarBuilderService(
            $c->get(CourseService::class)
        ));
        $this->set(EmailVerificationService::class, fn($c) => new EmailVerificationService(
            $c->get(TransactionManagerInterface::class),
            $c->get(EmailVerificationRepositoryInterface::class),
            $c->get(UserRepositoryInterface::class)
        ));
        $this->set(RegistrationCodeService::class, fn($c) => new RegistrationCodeService(
            $c->get(RegistrationCodeRepositoryInterface::class),
            $c->get(CourseRepositoryInterface::class)
        ));
        $this->set(AssetsService::class, fn($c) => new AssetsService(
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            __DIR__ . '/../assets',
            '/assets'
        ));

        $this->set(PasswordResetService::class, fn($c) => new PasswordResetService(
            $c->get(UserRepositoryInterface::class),
            $c->get(PasswordResetsRepositoryInterface::class),
            $c->get(MailerInterface::class),
            $c->get(TransactionManagerInterface::class),
            $c->get(LoggerInterface::class)
        ));

        // Logger (singleton)
        $this->set(LoggerInterface::class, fn() => AppLogger::getInstance());
    }
}
