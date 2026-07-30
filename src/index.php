<?php
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/csrf.php';

$container = Container::getInstance();
$app = $container->get(HomeController::class);
$app->index();