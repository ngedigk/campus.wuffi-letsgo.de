<?php
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
        

        // Services
        $this->set(AuthService::class, fn($c) => new AuthService($c->get(PDO::class)));
        $this->set(UserService::class, fn($c) => new UserService($c->get(UserRepository::class)));
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
            $c->get(AccessCodeRepository::class)
        ));

        // Helpers
        $this->set(ViewRenderer::class, fn() => new ViewRenderer(__DIR__));

        // Controllers
        $this->set(AdminController::class, fn($c) => new AdminController(
            $c->get(CourseService::class),
            $c->get(UserService::class),
            $c->get(AccessCodeRepository::class),
            $c->get(SlideService::class),
            $c->get(ModuleService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class)
        ));
        $this->set(AuthController::class, fn($c) => new AuthController(
            $c->get(DashboardService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class)
        ));
        $this->set(CourseController::class, fn($c) => new CourseController(
            $c->get(CourseService::class),    
            $c->get(ProgressService::class),
            $c->get(QuizService::class),
            $c->get(ViewRenderer::class),
            $c->get(AuthService::class)
        ));
    }

    public function set(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("Binding not found for {$abstract}");
        }

        $this->instances[$abstract] = $this->bindings[$abstract]($this);
        return $this->instances[$abstract];
    }
}