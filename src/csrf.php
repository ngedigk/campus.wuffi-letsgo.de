<?php

function csrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    // Generate token only once per session/page load
    if (empty($_SESSION['csrf_tokens'])) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$token] = time();
    }

    // Cleanup expired tokens (older than 1 hour)
    $threshold = time() - 3600;
    $_SESSION['csrf_tokens'] = array_filter(
        $_SESSION['csrf_tokens'],
        fn($timestamp) => $timestamp > $threshold
    );

    $lastToken = array_key_last($_SESSION['csrf_tokens']);
    return $lastToken !== null ? (string) $lastToken : '';
}

function validateCsrf(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $submittedToken = $_POST['csrf_token'] ?? '';
    $validTokens = $_SESSION['csrf_tokens'] ?? [];

    if (empty($submittedToken) || !isset($validTokens[$submittedToken]) || time() - $validTokens[$submittedToken] > 3600) {
        error_log('CSRF validation failed');
        exit('Invalid or expired CSRF token.');
    }

    unset($_SESSION['csrf_tokens'][$submittedToken]);
}