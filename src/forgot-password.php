<?php
require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(ForgotPasswordController::class);
$controller->index();

