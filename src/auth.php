<?php
function applySecurityHeaders()
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('X-Frame-Options: DENY');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => filter_var(getenv('APP_ENV') !== 'local', FILTER_VALIDATE_BOOLEAN),
    'httponly' => true,
    'samesite' => 'Strict'
]);

applySecurityHeaders();
session_start();

function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {

        header("Location: index.php");
        exit;

    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin(PDO $pdo): bool
{
    $user = currentUser($pdo);

    return (bool)($user['is_admin'] ?? 0);
}

function currentUser(PDO $pdo): ?array
{
    static $user = null;

    if ($user !== null) {
        return $user;
    }

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare("
        SELECT id, email, is_admin
        FROM users
        WHERE id = ?
    ");

    $stmt->execute([$_SESSION['user_id']]);

    $user = $stmt->fetch() ?: null;

    return $user;
}