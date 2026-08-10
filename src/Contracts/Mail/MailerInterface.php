<?php

namespace App\Contracts\Mail;

interface MailerInterface
{
    public function send(
        string $to,
        string $subject,
        string $htmlBody
    ): void;
}