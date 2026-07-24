<?php

class AuthController
{
    
    public function __construct(
        private DashboardService $dashboardService,
        private ViewRenderer $viewRenderer,
        private AuthService $authService
    ) {}

    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        }

        $user = $this->authService->currentUser();

        $context = [
            'user' => $user,
            'isLoggedIn' => $this->authService->isLoggedIn(),
            'isAdmin' => $this->authService->isAdmin(),
            'loginError' => $_SESSION['login_error'] ?? null,
            'redeemError' => $_SESSION['redeem_error'] ?? null,
            'redeemSuccess' => $_SESSION['redeem_success'] ?? null,
            'additionalCss' => [],
        ];
        unset(
            $_SESSION['login_error'],
            $_SESSION['redeem_error'],
            $_SESSION['redeem_success']
        );

        if ($user) {
            $this->renderDashboard($context);
        } else {
            $this->renderLoginForm($context);
        }
    }

    private function handleLogin(): void
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            $_SESSION['login_error'] = 'Bitte füllen Sie alle Felder aus.';
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $pdo = Database::getInstance();

        if ($this->authService->isIpBlocked()) {
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
                $this->authService->recordFailedLogin();
                return;
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = (int)$user['is_admin'];
            $this->authService->clearOldAttempts();
            header("Location: index.php");
            exit;
        } else {
            $this->authService->recordFailedLogin();
            $_SESSION['login_error'] = 'E-Mail oder Passwort ungültig';
        }
    }

    private function renderDashboard(array $context): void
    {
        $courses = $this->dashboardService->getUserDashboardData($context['user']['id']);

        $viewData = [
            'pageTitle' => 'Dashboard',
            ...$context,
            'courses' => $courses,
            'additionalCss' => [...$context['additionalCss'], '/assets/css/dashboard.css']
        ];

        $this->viewRenderer->renderWithTemplate('dashboard', $viewData);
    }

    private function renderLoginForm(array $context): void
    {
        $viewData = [
            'pageTitle' => 'Login',
            ...$context,
        ];

        $this->viewRenderer->renderWithTemplate('login-form', $viewData);
    }
}