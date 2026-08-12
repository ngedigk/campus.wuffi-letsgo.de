<?php

namespace App\Infrastructure\Mail;

use App\Contracts\Mail\MailerInterface;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

use Psr\Log\LoggerInterface;

use App\Exceptions\EmailSendException;

class PHPMailerMailer implements MailerInterface
{
    private PHPMailer $mailer;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->mailer = new PHPMailer(true);
        $this->configureSMTP();
        $this->configureDefaults();
    }

    private function configureSMTP(): void
    {
        $this->mailer->isSMTP();

        $host = $_ENV['SMTP_HOST'] ?: 'localhost';
        $port = (int) ($_ENV['SMTP_PORT'] ?: 1025);

        $this->mailer->Host = $host;
        $this->mailer->Port = $port;

        $smtpAuth = filter_var(
            $_ENV['SMTP_AUTH'] ?: 'false',
            FILTER_VALIDATE_BOOLEAN
        );

        $this->mailer->SMTPAuth = $smtpAuth;

        if ($smtpAuth) {
            $this->mailer->Username = $_ENV['SMTP_USER'] ?: '';
            $this->mailer->Password = $_ENV['SMTP_PASS'] ?: '';

            $encryption = strtolower(
                $_ENV['SMTP_ENCRYPTION'] ?: 'tls'
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
            $this->logger->error('Mailer Error', ['exception' => $e]);
            throw new EmailSendException(
                "E-Mail Versand fehlgeschlagen.",
                previous: $e
            );
        }
    }
}