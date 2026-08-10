<?php

namespace App\Container;

use App\Contracts\Repositories\AccessCodeRepositoryInterface;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Repositories\EmailVerificationRepositoryInterface;
use App\Contracts\Repositories\ModuleRepositoryInterface;
use App\Contracts\Repositories\PasswordResetsRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionChoiceRepositoryInterface;

use App\Repositories\AccessCodeRepository;
use App\Repositories\AuthRepository;
use App\Repositories\CourseRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\PasswordResetsRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Repositories\RegistrationCodeRepository;
use App\Repositories\SlideRepository;
use App\Repositories\UserCourseRepository;
use App\Repositories\UserRepository;

use \PDO;

trait RepositoriesBindings
{
    private function registerRepositories(): void
    {
        $this->set(CourseRepositoryInterface::class, fn($c) => new CourseRepository($c->get(PDO::class)));
        $this->set(ModuleRepositoryInterface::class, fn($c) => new ModuleRepository($c->get(PDO::class)));
        $this->set(SlideRepository::class, fn($c) => new SlideRepository($c->get(PDO::class)));
        $this->set(QuizQuestionRepository::class, fn($c) => new QuizQuestionRepository($c->get(PDO::class)));
        $this->set(QuestionChoiceRepositoryInterface::class, fn($c) => new QuestionChoiceRepository($c->get(PDO::class)));
        $this->set(ProgressRepositoryInterface::class, fn($c) => new ProgressRepository($c->get(PDO::class)));
        $this->set(UserRepository::class, fn($c) => new UserRepository($c->get(PDO::class)));
        $this->set(RegistrationCodeRepository::class, fn($c) => new RegistrationCodeRepository($c->get(PDO::class)));
        $this->set(EmailVerificationRepositoryInterface::class, fn($c) => new EmailVerificationRepository($c->get(PDO::class)));
        $this->set(AccessCodeRepositoryInterface::class, fn($c) => new AccessCodeRepository($c->get(PDO::class)));
        $this->set(UserCourseRepository::class, fn($c) => new UserCourseRepository($c->get(PDO::class)));
        $this->set(AuthRepositoryInterface::class, fn($c) => new AuthRepository($c->get(PDO::class)));
        $this->set(PasswordResetsRepositoryInterface::class, fn($c) => new PasswordResetsRepository($c->get(PDO::class)));
    }
}
