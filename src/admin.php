<?php
use App\Container\Container;
use App\Services\AuthService;
use App\Controller\AdminController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$authService = $container->get(AuthService::class);
$authService->requireLogin();

if (!$authService->isAdmin()) {
    $_SESSION['admin_error'] = 'Sie haben keine Berechtigung administative Funktionen zu verwalten.';
    header('Location: index.php');
    exit;
}

$adminController = $container->get(AdminController::class);
$adminController->handle($_GET['page'] ?? 'dashboard');
exit;