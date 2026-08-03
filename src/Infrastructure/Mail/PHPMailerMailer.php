<?php

namespace App\Infrastructure\Mail;

use App\Contracts\Mailer;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

class PHPMailerMailer implements Mailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureSMTP();
        $this->configureDefaults();
    }

    private function configureSMTP(): void
    {
        $host = getenv('SMTP_HOST') ?: 'localhost';
        $port = (int)(getenv('SMTP_PORT') ?: 587);

        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->Port = $port;

        if ($host === 'mailhog' || $host === 'localhost') {
            $this->mailer->SMTPAuth = false;
            $this->mailer->SMTPSecure = ''; 
            $this->mailer->SMTPAutoTLS = false;
        } else {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = getenv('SMTP_USER') ?: '';
            $this->mailer->Password = getenv('SMTP_PASS') ?: '';
            
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            
            if ($port === 465) {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }
        }
        
    }

    private function configureDefaults(): void
    {
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom(MAIL_FROM, WEBSITE_NAME);
    }

    public function send(string $to, string $subject, string $htmlBody): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();

        $this->mailer->addAddress($to);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $htmlBody;

        try {
            $this->mailer->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            throw new Exception("E-Mail Versand fehlgeschlagen.");
        }
    }
}