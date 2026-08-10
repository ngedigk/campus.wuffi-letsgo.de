<?php

namespace App\Container;

use App\Contracts\Mailer;
use App\Contracts\TransactionManager;
use App\Contracts\UserServiceInterface;
use App\Contracts\AuthRepositoryInterface;
use App\Contracts\AccessCodeRepositoryInterface;
use App\Contracts\CourseRepositoryInterface;
use App\Contracts\EmailVerificationRepositoryInterface;

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

use App\Repositories\ModuleRepository;
use App\Repositories\SlideRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\RegistrationCodeRepository;
use App\Repositories\UserCourseRepository;
use App\Repositories\UserRepository;
use App\Repositories\PasswordResetsRepository;

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
        $this->set(UserServiceInterface::class, fn($c) => new UserService($c->get(UserRepository::class)));
        $this->set(AuthService::class, fn($c) => new AuthService($c->get(UserServiceInterface::class), $c->get(AuthRepositoryInterface::class)));
        $this->set(CourseService::class, fn($c) => new CourseService(
            $c->get(UuidService::class),
            $c->get(CourseRepositoryInterface::class),
            $c->get(ModuleRepository::class),
            $c->get(SlideRepository::class)
        ));
        $this->set(ModuleService::class, fn($c) => new ModuleService($c->get(ModuleRepository::class)));
        $this->set(SlideService::class, fn($c) => new SlideService(
            $c->get(SlideRepository::class),
            $c->get(QuizQuestionRepository::class)
        ));
        $this->set(QuizService::class, fn($c) => new QuizService($c->get(QuizQuestionRepository::class)));
        $this->set(QuizQuestionService::class, fn($c) => new QuizQuestionService(
            $c->get(QuizQuestionRepository::class),
            $c->get(QuestionChoiceService::class)
        ));
        $this->set(QuestionChoiceService::class, fn($c) => new QuestionChoiceService($c->get(QuestionChoiceRepository::class)));
        $this->set(AdminCourseManagementService::class, fn($c) => new AdminCourseManagementService(
            $c->get(CourseService::class),
            $c->get(ModuleService::class),
            $c->get(SlideService::class),
            $c->get(QuizQuestionService::class),
            $c->get(QuestionChoiceService::class),
            $c->get(AssetsService::class),
            $c->get(TransactionManager::class)
        ));
        $this->set(ProgressService::class, fn($c) => new ProgressService($c->get(ProgressRepository::class)));
        $this->set(AccessCodeService::class, fn($c) => new AccessCodeService($c->get(AccessCodeRepositoryInterface::class)));
        $this->set(RedeemService::class, fn($c) => new RedeemService(
            $c->get(TransactionManager::class),
            $c->get(AccessCodeRepositoryInterface::class),
            $c->get(UserCourseRepository::class)
        ));
        $this->set(DashboardService::class, fn($c) => new DashboardService(
            $c->get(CourseService::class),
            $c->get(ProgressService::class)
        ));
        $this->set(RegistrationService::class, fn($c) => new RegistrationService(
            $c->get(TransactionManager::class),
            $c->get(Mailer::class),
            $c->get(UserRepository::class),
            $c->get(EmailVerificationRepositoryInterface::class),
            $c->get(RegistrationCodeRepository::class),
            $c->get(AccessCodeRepositoryInterface::class),
            $c->get(UuidService::class)
        ));
        $this->set(CourseNavigationService::class, fn($c) => new CourseNavigationService(
            $c->get(CourseService::class)
        ));
        $this->set(CourseSidebarBuilderService::class, fn($c) => new CourseSidebarBuilderService(
            $c->get(CourseService::class)
        ));
        $this->set(EmailVerificationService::class, fn($c) => new EmailVerificationService(
            $c->get(TransactionManager::class),
            $c->get(EmailVerificationRepositoryInterface::class),
            $c->get(UserRepository::class)
        ));
        $this->set(RegistrationCodeService::class, fn($c) => new RegistrationCodeService(
            $c->get(RegistrationCodeRepository::class),
            $c->get(CourseRepositoryInterface::class)
        ));
        $this->set(AssetsService::class, fn($c) => new AssetsService(
            $c->get(AuthService::class),
            $c->get(CsrfService::class),
            __DIR__ . '/../assets',
            '/assets'
        ));

        $this->set(PasswordResetService::class, fn($c) => new PasswordResetService(
            $c->get(UserRepository::class),
            $c->get(PasswordResetsRepository::class),
            $c->get(Mailer::class),
            $c->get(TransactionManager::class),
        ));
    }
}
