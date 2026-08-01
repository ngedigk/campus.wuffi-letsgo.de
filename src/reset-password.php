<?php
use App\Container\Container;
use App\Controller\ResetPasswordController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(ResetPasswordController::class);
$controller->index();