<?php

declare(strict_types=1);

namespace App\Framework\Monolog;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\PsrLogMessageProcessor;

final class StdErrStreamHandler extends AbstractHandler
{
    private const string STDERR_STREAM = 'php://stderr';
    private const string APP_CHANNEL_NAME = 'app';
    private const array APP_LOG_LEVELS = [Level::Info, Level::Notice, Level::Warning];
    private StreamHandler $appHandler;
    private FingersCrossedHandler $errorHandler;

    public function __construct()
    {
        parent::__construct();

        $psrLogMessageProcessor = new PsrLogMessageProcessor();

        $this->appHandler = new StreamHandler(self::STDERR_STREAM);
        $this->appHandler->setFormatter(new JsonFormatter());
        $this->appHandler->pushProcessor($psrLogMessageProcessor);

        $errorHandler = new StreamHandler(self::STDERR_STREAM);
        $errorHandler->setFormatter(new JsonFormatter());
        $errorHandler->pushProcessor($psrLogMessageProcessor);
        $this->errorHandler = new FingersCrossedHandler($errorHandler, new IgnoreHttpErrorActivationStrategy());
    }

    public function handle(LogRecord $record): bool
    {
        if (self::APP_CHANNEL_NAME === $record->channel && \in_array($record->level, self::APP_LOG_LEVELS, true)) {
            return $this->appHandler->handle($record);
        }

        return $this->errorHandler->handle($record);
    }

    public function reset(): void
    {
        $this->appHandler->reset();
        $this->errorHandler->reset();
    }
}
