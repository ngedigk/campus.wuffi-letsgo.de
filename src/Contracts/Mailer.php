<?php

namespace App\Contracts;

interface Mailer
{
    public function send(
        string $to,
        string $subject,
        string $htmlBody
    ): void;
}