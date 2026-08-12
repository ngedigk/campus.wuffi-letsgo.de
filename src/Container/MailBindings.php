<?php

namespace App\Container;

use App\Contracts\Mail\MailerInterface;

use Psr\Log\LoggerInterface;

use App\Infrastructure\Mail\PHPMailerMailer;

trait MailBindings
{
    private function registerMail(): void
    {
        $this->set(
            MailerInterface::class,
            fn ($c) => new PHPMailerMailer($c->get(LoggerInterface::class))
        );
    }
}