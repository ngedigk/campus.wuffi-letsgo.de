<?php
use App\Container;
use App\Controller\ProfileController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$controller = $container->get(ProfileController::class);
$controller->index();