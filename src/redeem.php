<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/Container.php';

$container = Container::getInstance();

$authService = $container->get(AuthService::class);
$authService->requireLogin(__DIR__);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

validateCsrf();

$code = trim($_POST['code'] ?? '');

if ($code === '') {
    redeemError("Invalid code.");
}

try {
    $redeemService = $container->get(RedeemService::class);


    $redeemService->redeem(
        $_SESSION['user_id'],
        $code
    );

    redeemSuccess(
        "Course redeemed successfully."
    );

} catch (RedeemException $e) {

    redeemError($e->getMessage());

} catch (Throwable $e) {
    error_log($e);

    redeemError("Something went wrong. Please try again later.");
}

function redeemError(string $message): never
{
    $_SESSION['redeem_error'] = $message;

    header("Location: index.php");
    exit;
}


function redeemSuccess(string $message): never
{
    $_SESSION['redeem_success'] = $message;

    header("Location: index.php");
    exit;
}