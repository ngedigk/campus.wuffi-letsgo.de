<?php

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