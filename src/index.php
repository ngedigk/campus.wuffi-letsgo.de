<?php
use App\Container\Container;

use App\Routing\Router;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$router = new Router($container);

require __DIR__ . '/routes/routes.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);