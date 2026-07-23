<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';
require_once __DIR__ . '/Container.php';


$container = Container::getInstance();

$authService = $container->get(AuthService::class);
$authService->requireLogin(__DIR__);

if (!$authService->isAdmin()) {
    $_SESSION['admin_error'] = 'You do not have permission to manage admin features.';
    header('Location: index.php');
    exit;
}

$adminController = $container->get(AdminController::class);
$adminController->handle($_GET['page'] ?? 'dashboard');
exit;