<?php

namespace App;

use PDO;
use App\Repositories\CourseRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\SlideRepository;
use App\Repositories\QuizRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\UserRepository;
use App\Repositories\RegistrationCodeRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\AccessCodeRepository;
use App\Repositories\UserCourseRepository;
use App\Repositories\AuthRepository;
use App\Repositories\PasswordResetsRepository;
use App\Services\CsrfService;
use App\Services\UuidService;
use App\Services\MailerService;
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\ModuleService;
use App\Services\SlideService;
use App\Services\QuizService;
use App\Services\ProgressService;
use App\Services\RedeemService;
use App\Services\DashboardService;
use App\Services\RegistrationService;
use App\Services\CourseSidebarBuilderService;
use App\Services\EmailVerificationService;
use App\Services\RegistrationCodeService;
use App\Controller\Admin\AdminDashboardController;
use App\Controller\Admin\AdminCoursesController;
use App\Controller\Admin\AdminAccessCodesController;
use App\Controller\Admin\AdminUsersController;
use App\Controller\Admin\AdminRegistrationCodesController;
use App\Controller\AdminController;
use App\Controller\DashboardController;
use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\RegistrationController;
use App\Controller\ForgotPasswordController;
use App\Controller\ResetPasswordController;
use App\Controller\CourseController;
use App\Controller\ProfileController;
use App\Helpers\ViewRenderer;

class Container
{
    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];

    private function __construct() {}

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->registerBindings();
        }
        return self::$instance;
    }

    private function registerBindings(): void
    {
        $pdo = Database::getInstance();

        // Database (Singleton)
        $this->instances[PDO::class] = $pdo;

        // Repositories
        $this->set(CourseRepository::class, fn($c) => new CourseRepository($c->get(PDO::class)));
        $this->set(ModuleRepository::class, fn($c) => new ModuleRepository($c->get(PDO::class)));
        $this->set(SlideRepository::class, fn($c) => new SlideRepository($c->get(PDO::class)));
        $this->set(QuizRepository::class, fn($c) => new QuizRepository($c->get(PDO::class)));
        $this->set(ProgressRepository::class, fn($c) => new ProgressRepository($c->get(PDO::class)));
        $this->set(UserRepository::class, fn($c) => new UserRepository($c->get(PDO::class)));
        $this->set(RegistrationCodeRepository::class, fn($c) => new RegistrationCodeRepository($c->get(PDO::class))); 
        $this->set(EmailVerificationRepository::class, fn($c) => new EmailVerificationRepository($c->get(PDO::class))); 
        $this->set(AccessCodeRepository::class, fn($c) => new AccessCodeRepository($c->get(PDO::class)));
        $this->set(UserCourseRepository::class, fn($c) => new UserCourseRepository($c->get(PDO::class)));
        $this->set(AuthRepository::class, fn($c) => new AuthRepository($c->get(PDO::class)));
        $this->set(PasswordResetsRepository::class, fn($c) => new PasswordResetsRepository($c->get(PDO::class)));
        
        // Services
        $this->set(CsrfService::class, fn() => new CsrfService());
        $this->set(UuidService::class, fn() => new UuidService());
        $this->set(MailerService::class, fn() => new MailerService());
        $this->set(UserService::class, fn($c) => new UserService($c->get(UserRepository::class)));
        $this->set(AuthService::class, fn($c) => new AuthService($c->get(UserService::class), $c->get(AuthRepository::class)));
        $this->set(CourseService::class, fn($c) => new CourseService(
            $c->get(CourseRepository::class), $c->get(ModuleRepository::class), $c->get(SlideRepository::class)
        ));
        $this->set(ModuleService::class, fn($c) => new ModuleService($c->get(ModuleRepository::class)));
        $this->set(SlideService::class, fn($c) => new SlideService($c->get(SlideRepository::class)));
        $this->set(QuizService::class, fn($c) => new QuizService($c->get(QuizRepository::class)));
        $this->set(ProgressService::class, fn($c) => new ProgressService($c->get(ProgressRepository::class)));
        $this->set(RedeemService::class, fn($c) => new RedeemService(
            $c->get(PDO::class),
            $c->get(AccessCodeRepository::class),
            $c->get(UserCourseRepository::class)
        ));
        $this->set(DashboardService::class, fn($c) => new DashboardService(
            $c->get(CourseService::class), $c->get(ProgressService::class)
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

        // Helpers
        $this->set(ViewRenderer::class, fn() => new ViewRenderer(__DIR__));

        // Controllers
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

    private function normalizeAbstract(string $abstract): string
    {
        if ($abstract === '' || str_contains($abstract, '\\')) {
            return $abstract;
        }

        $namespaced = 'App\\' . $abstract;

        if (class_exists($namespaced)) {
            return $namespaced;
        }

        return $abstract;
    }

    public function set(string $abstract, callable $concrete): void
    {
        $normalized = $this->normalizeAbstract($abstract);
        $this->bindings[$normalized] = $concrete;
    }

    public function get(string $abstract)
    {
        $normalized = $this->normalizeAbstract($abstract);

        if (isset($this->instances[$normalized])) {
            return $this->instances[$normalized];
        }

        if (!isset($this->bindings[$normalized])) {
            throw new \Exception("Binding not found for {$abstract}");
        }

        $this->instances[$normalized] = $this->bindings[$normalized]($this);
        return $this->instances[$normalized];
    }
}