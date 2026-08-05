<?php
use App\Container\Container;
use App\Services\AuthService;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/Container/Container.php';
require_once __DIR__ . '/Database/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => filter_var($_SERVER['HTTPS'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_start();
}

$container = Container::getInstance();

$authService = $container->get(AuthService::class);
$authService->start();