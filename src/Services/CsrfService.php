<?php

namespace App\Services;

use Exception;

class CsrfService
{
    public function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }

        if (empty($_SESSION['csrf_tokens'])) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf_tokens'][$token] = time();
        }

        $threshold = time() - 3600;
        $_SESSION['csrf_tokens'] = array_filter(
            $_SESSION['csrf_tokens'],
            fn($timestamp) => $timestamp > $threshold
        );

        $lastToken = array_key_last($_SESSION['csrf_tokens']);
        return $lastToken !== null ? (string) $lastToken : '';
    }

    public function validateToken(string $token): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $validTokens = $_SESSION['csrf_tokens'] ?? [];

        if (empty($token) || !isset($validTokens[$token]) || time() - $validTokens[$token] > 3600) {
            error_log('CSRF validation failed');
            throw new Exception('Invalid or expired CSRF token.');
        }

        unset($_SESSION['csrf_tokens'][$token]);
    }
    
    public function validateAndHandle(string $token, string $redirectUrl): void
    {
        try {
            $this->validateToken($token);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Invalid or expired CSRF token.';
            header("Location: $redirectUrl");
            exit;
        }
    }
}