<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/rate_limit.php';

require_once __DIR__ . '/controller/AuthController.php';

$app = new AuthController(__DIR__);
$app->handle();