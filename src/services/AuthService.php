<?php

class AuthService
{
    private ?array $userCache = null;

    public function __construct(
        private UserService $userService,
        private AuthRepository $authRepository
    ) {}

    public function start(): void
    {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => filter_var($_SERVER['HTTPS'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
        header('X-Frame-Options: DENY');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        session_start();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function requireLogin(string $redirectPath = 'index.php'): void
    {
        if (!$this->isLoggedIn()) {
            header("Location: {$redirectPath}");
            exit;
        }
    }

    public function currentUser(): ?array
    {
        if ($this->userCache !== null) {
            return $this->userCache;
        }

        $id = $_SESSION['user_id'] ?? null;
        if (!$id) {
            return $this->userCache = null;
        }

        return $this->userCache = $this->userService->get($id);
    }

    public function isAdmin(): bool
    {
        $user = $this->currentUser();
        return (bool)($user['is_admin'] ?? 0);
    }

    public function isIpBlocked($limit = 5, $windowMinutes = 10)
    {
        $ip = $this->getClientIp();

        $count = $this->authRepository->getLoginAttemptAmount($ip, $windowMinutes);

        return $count >= $limit;
    }

    public function recordFailedLogin()
    {
        $ip = $this->getClientIp();
        $this->authRepository->recordFailedLogin($ip);
    }

    public function clearOldAttempts($windowMinutes = 10)
    {
        $this->authRepository->clearOldAttempts($windowMinutes);
    }

    public function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}