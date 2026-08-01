<?php
use App\Container;
use App\Services\AuthService;
use App\Controller\AdminController;

require __DIR__ . '/bootstrap.php';

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