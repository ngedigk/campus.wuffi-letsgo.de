<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/functions.php';

require_once __DIR__ . '/repositories/AccessCodeRepository.php';
require_once __DIR__ . '/repositories/UserCourseRepository.php';
require_once __DIR__ . '/services/RedeemService.php';

requireLogin();

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

    $accessCodes = new AccessCodeRepository($pdo);

    $userCourses = new UserCourseRepository($pdo);

    $service = new RedeemService(
        $pdo,
        $accessCodes,
        $userCourses
    );

    $service->redeem(
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