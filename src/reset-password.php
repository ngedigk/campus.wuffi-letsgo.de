<?php
require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(ResetPasswordController::class);
$controller->index();
