<?php

namespace App\Services;

use App\Exceptions\CsrfException;

class CsrfService
{
    public function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            empty($_SESSION['csrf_token']) ||
            empty($_SESSION['csrf_token_created']) ||
            time() - $_SESSION['csrf_token_created'] > 3600
        ) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_created'] = time();
        }

        return $_SESSION['csrf_token'];
    }

    public function validateToken(string $token): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $validToken = $_SESSION['csrf_token'] ?? '';
        $created = $_SESSION['csrf_token_created'] ?? 0;

        if (
            empty($token) ||
            empty($validToken) ||
            !hash_equals($validToken, $token) ||
            time() - $created > 3600
        ) {
            throw new CsrfException(
                'Ungültiger oder abgelaufener CSRF-Token.'
            );
        }
    }
}