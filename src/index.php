<?php
require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$app = $container->get(HomeController::class);
$app->index();