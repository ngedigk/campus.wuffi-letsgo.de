<?php

namespace App\Container;

use App\Contracts\Mailer;

use App\Infrastructure\Mail\PHPMailerMailer;

trait MailBindings
{
    private function registerMail(): void
    {
        $this->set(
            Mailer::class,
            fn ($c) => new PHPMailerMailer()
        );
    }
}