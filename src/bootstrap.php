<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => filter_var($_SERVER['HTTPS'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/Container.php';
require_once __DIR__ . '/Database.php';

$container = Container::getInstance();
$authService = $container->get(AuthService::class);
$authService->start();