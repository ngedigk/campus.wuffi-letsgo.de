<?php

namespace App\Controller\Admin;

use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\CourseService;
use App\Services\SlideService;
use App\Services\ModuleService;
use App\Services\RegistrationCodeService;
use App\Services\QuizService;
use App\Services\UuidService;
use App\Services\UserService;
use App\Services\QuizQuestionService;
use App\Repositories\AccessCodeRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuestionChoiceRepository;
use App\Helpers\ViewRenderer;
use App\Dto\User;

abstract class AdminPageController
{
    public function __construct(
        protected CourseService $courseService,
        protected UserService $userService,
        protected AccessCodeRepository $accessCodeRepository,
        protected SlideService $slideService,
        protected ModuleService $moduleService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected CsrfService $csrfService,
        protected UuidService $uuidService,
        protected RegistrationCodeService $registrationCodeService,
        protected QuizQuestionRepository $quizQuestionRepository,
        protected QuestionChoiceRepository $questionChoicesRepository,
        protected QuizService $quizService,
        protected QuizQuestionService $quizQuestionService
    ) {}

    protected function buildContext(User $user): array
    {
        $context = [
            'csrfToken' => $this->csrfService->generateToken(),
            'user' => $user,
            'isAdmin' => $this->authService->isAdmin(),
            'adminError' => $_SESSION['admin_error'] ?? null,
            'adminSuccess' => $_SESSION['admin_success'] ?? null,
            'additionalCss' => ['/assets/css/admin.css'],
            'additionalJs' => ['/assets/js/admin/general.js'],
            'allCourses' => $this->courseService->getAll(),
        ];

        unset(
            $_SESSION['admin_error'],
            $_SESSION['admin_success']
        );

        return $context;
    }
}