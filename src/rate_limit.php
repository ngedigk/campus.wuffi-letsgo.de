<?php

function getClientIp()
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function isIpBlocked(PDO $pdo, $limit = 5, $windowMinutes = 10)
{
    $ip = getClientIp();

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM login_attempts
        WHERE ip = ?
        AND attempted_at > (NOW() - INTERVAL ? MINUTE)
    ");

    $stmt->execute([$ip, $windowMinutes]);

    $count = $stmt->fetchColumn();

    return $count >= $limit;
}

function recordFailedLogin(PDO $pdo)
{
    $ip = getClientIp();

    $stmt = $pdo->prepare("
        INSERT INTO login_attempts (ip)
        VALUES (?)
    ");

    $stmt->execute([$ip]);
}

function clearOldAttempts(PDO $pdo, $windowMinutes = 10)
{
    $stmt = $pdo->prepare("
        DELETE FROM login_attempts
        WHERE attempted_at < (NOW() - INTERVAL ? MINUTE)
    ");

    $stmt->execute([$windowMinutes]);
}