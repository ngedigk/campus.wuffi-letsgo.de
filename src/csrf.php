<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
    $_SESSION['csrf_tokens'] = [];
}

$pageToken = bin2hex(random_bytes(32));

$_SESSION['csrf_tokens'][$pageToken] = time();

$cleanupThreshold = time() - 3600;
foreach ($_SESSION['csrf_tokens'] as $token => $timestamp) {
    if ($timestamp < $cleanupThreshold) {
        unset($_SESSION['csrf_tokens'][$token]);
    }
}

function csrfToken()
{
    $tokens = $_SESSION['csrf_tokens'] ?? [];
    $token = array_key_last($tokens);
    return $token !== null ? (string) $token : '';
}

function validateCsrf()
{
    $submittedToken = $_POST['csrf_token'] ?? '';
    $validTokens = $_SESSION['csrf_tokens'] ?? [];

    if (empty($submittedToken) || !isset($validTokens[$submittedToken]) || time() - $validTokens[$submittedToken] > 3600) {
        error_log('CSRF validation failed');
        exit('Invalid or expired CSRF token.');
    }

    unset($_SESSION['csrf_tokens'][$submittedToken]);
}