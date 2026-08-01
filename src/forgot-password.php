<?php
use App\Container\Container;
use App\Controller\ForgotPasswordController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(ForgotPasswordController::class);
$controller->index();