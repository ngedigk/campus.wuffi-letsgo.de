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
        $this->mailer->isSMTP();

        $host = getenv('SMTP_HOST') ?: 'localhost';
        $port = (int) (getenv('SMTP_PORT') ?: 1025);

        $this->mailer->Host = $host;
        $this->mailer->Port = $port;

        $smtpAuth = filter_var(
            getenv('SMTP_AUTH') ?: 'false',
            FILTER_VALIDATE_BOOLEAN
        );

        $this->mailer->SMTPAuth = $smtpAuth;

        if ($smtpAuth) {
            $this->mailer->Username = getenv('SMTP_USER') ?: '';
            $this->mailer->Password = getenv('SMTP_PASS') ?: '';

            $encryption = strtolower(
                getenv('SMTP_ENCRYPTION') ?: 'tls'
            );

            switch ($encryption) {
                case 'ssl':
                case 'smtps':
                    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    break;

                case 'tls':
                case 'starttls':
                    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    break;

                case '':
                case 'none':
                    $this->mailer->SMTPSecure = '';
                    break;

                default:
                    throw new \RuntimeException(
                        "Unsupported SMTP encryption: {$encryption}"
                    );
            }
        } else {
            $this->mailer->SMTPSecure = '';
            $this->mailer->SMTPAutoTLS = false;
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