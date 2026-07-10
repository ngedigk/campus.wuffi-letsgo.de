<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

function sendMail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.hostkraft.de';
        $mail->SMTPAuth = true;

        $mail->Username = getenv('SMTP_USER');
        $mail->Password = getenv('SMTP_PASSWORD');

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(MAIL_FROM, 'Your Website');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;

    }
}