<?php
use App\Container\Container;
use App\Services\AuthService;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require_once __DIR__ . '/config.php';

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