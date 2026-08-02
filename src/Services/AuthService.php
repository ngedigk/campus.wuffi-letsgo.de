<?php

namespace App\Services;

use App\Repositories\AuthRepository;

use App\Dto\User;
use App\Dto\AuthenticationResult;

class AuthService
{
    private ?User $userCache = null;

    public function __construct(
        private UserService $userService,
        private AuthRepository $authRepository
    ) {}

    public function start(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
        header('X-Frame-Options: DENY');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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

    public function currentUser(): ?User
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

    public function getCurrentUserId(): ?string
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function isAdmin(): bool
    {
        $user = $this->currentUser();
        return $user?->isAdmin ?? false;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userService->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->passwordHash)) {
            return null;
        }

        return $user;
    }

    public function login(string $email, string $password): AuthenticationResult
    {
        if ($this->isIpBlocked()) {
            return new AuthenticationResult(
                success: false,
                error: 'Zu viele Anmeldeversuche. Bitte versuchen Sie es später nochmal.'
            );
        }

        $user = $this->userService->findByEmail($email);

        if (!$user || !password_verify($password, $user->passwordHash)) {
            $this->recordFailedLogin();

            return new AuthenticationResult(
                success: false,
                error: 'E-Mail oder Passwort ungültig'
            );
        }

        if ($user->emailVerified !== true) {
            $this->recordFailedLogin();

            return new AuthenticationResult(
                success: false,
                error: 'Bestätigen Sie bitte erst Ihre E-Mail Adresse.'
            );
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;
        $_SESSION['is_admin'] = (int)$user->isAdmin;

        $this->userCache = $user;

        $this->clearOldAttempts();

        return new AuthenticationResult(true);
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

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }
    }
}