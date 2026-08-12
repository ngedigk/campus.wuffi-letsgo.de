<?php

namespace App\Infrastructure\Logging;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\AbstractLogger;

class AppLogger extends AbstractLogger
{
    private static ?AppLogger $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            $monolog = new Logger('WuffiLetsgo');

            $monolog->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM));

            $monolog->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

            self::$instance = new self($monolog);
        }

        return self::$instance;
    }

    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    private Logger $monolog;

    private function __construct(Logger $monolog)
    {
        $this->monolog = $monolog;
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if (!empty($context)) {
            $message .= ' ' . json_encode($context, JSON_THROW_ON_ERROR);
        }

        $this->monolog->log($level, $message, []);
    }
}
