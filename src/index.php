<?php
use App\Container\Container;
use App\Controller\HomeController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$app = $container->get(HomeController::class);
$app->index();