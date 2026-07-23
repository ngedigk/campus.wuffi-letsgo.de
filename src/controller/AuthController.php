<?php

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../csrf.php';
require_once __DIR__ . '/../rate_limit.php';
require_once __DIR__ . '/../Database.php';

require_once __DIR__ . '/../repositories/CourseRepository.php';
require_once __DIR__ . '/../repositories/ModuleRepository.php';
require_once __DIR__ . '/../repositories/SlideRepository.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/ProgressRepository.php';

require_once __DIR__ . '/../services/SlideService.php';
require_once __DIR__ . '/../services/ModuleService.php';
require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/CourseService.php';
require_once __DIR__ . '/../services/ProgressService.php';

class AuthController
{
    private CourseService $courseService;
    private ProgressService $progressService;
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->initServices();
    }

    private function initServices(): void
    {
        $pdo = Database::getInstance();
        
        $this->courseService = new CourseService(
            new CourseRepository($pdo),
            new ModuleRepository($pdo),
            new SlideRepository($pdo)
        );
        $this->progressService = new ProgressService(
            new ProgressRepository($pdo)
        );
    }

    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        }

        $pdo = Database::getInstance();
        $user = currentUser($pdo);
        $isAdmin = isAdmin($pdo);

        $loginError= $_SESSION['login_error'] ?? null;
        $redeemError = $_SESSION['redeem_error'] ?? null;
        $redeemSuccess = $_SESSION['redeem_success'] ?? null;
        unset(
            $_SESSION['login_error'],
            $_SESSION['redeem_error'],
            $_SESSION['redeem_success']
        );

        $context = [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'loginError' => $loginError,
            'redeemError' => $redeemError,
            'redeemSuccess' => $redeemSuccess,
            'additionalCss' => [],
        ];

        if ($user) {
            $this->renderDashboard($context);
        } else {
            $this->renderLoginForm($context);
        }
    }

    private function handleLogin(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $pdo = Database::getInstance();

        if (isIpBlocked($pdo)) {
            $_SESSION['login_error'] = 'Zu viele Anmeldeversuche. Bitte versuchen Sie es später nochmal.';
            return;
        }

        validateCsrf();

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ((int)$user['email_verified'] !== 1) {
                $_SESSION['login_error'] = 'Bestätigen Sie bitte erst Ihre E-Mail Adresse.';
                recordFailedLogin($pdo);
                return;
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = (int)$user['is_admin'];
            clearOldAttempts($pdo);
            header("Location: index.php");
            exit;
        } else {
            recordFailedLogin($pdo);
            $_SESSION['login_error'] = 'E-Mail oder Passwort ungültig';
        }
    }

    private function renderDashboard(array $context): void
    {
        $pageTitle = 'Dashboard';
        $user = $context['user'];

        $courses = $this->courseService->getAllForUser($user['id']);
        foreach ($courses as $course) {
            $course->isUnlocked = true;
            if ($course->prerequisiteCourseId) {
                $course->isUnlocked = $this->progressService->isCourseCompleted($user['id'], $course->prerequisiteCourseId) ? 1 : 0;
            }
            $course->isCompleted = $this->progressService->isCourseCompleted($user['id'], $course->uuid) ? 1 : 0;
        }

        $context['courses'] = $courses;
        $context['additionalCss'][] = '/assets/css/dashboard.css';

        extract([
            ...$context,
            'pageTitle' => $pageTitle
        ]);

        ob_start();
        require_once $this->basePath . '/views/dashboard.php';
        $content = ob_get_clean();

        require_once $this->basePath . '/template.php';
    }

    private function renderLoginForm(array $context): void
    {
        $pageTitle = 'Login';

        extract([
            ...$context,
            'pageTitle' => $pageTitle
        ]);

        ob_start();
        require_once $this->basePath . '/views/login-form.php';
        $content = ob_get_clean();

        require_once $this->basePath . '/template.php';
    }

    private function getPdo(): PDO
    {
        return $GLOBALS['pdo'] ?? new PDO('sqlite::memory:');
    }
}