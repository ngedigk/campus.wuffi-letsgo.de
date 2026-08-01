<?php
use App\Container\Container;
use App\Controller\RegistrationController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(RegistrationController::class);

$action = $_GET['action'] ?? 'index';

if ($action === 'verify') {
    $controller->verify($_GET['token'] ?? '');
} else {
    $controller->index();
}