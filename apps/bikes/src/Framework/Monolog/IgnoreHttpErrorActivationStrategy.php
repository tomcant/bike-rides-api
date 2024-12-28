<?php

declare(strict_types=1);

namespace App\Framework\Monolog;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class IgnoreHttpErrorActivationStrategy extends ErrorLevelActivationStrategy
{
    public function __construct()
    {
        parent::__construct(Level::Error);
    }

    public function isHandlerActivated(LogRecord $record): bool
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof HttpExceptionInterface) {
            return 500 <= $record['context']['exception']->getStatusCode();
        }

        return parent::isHandlerActivated($record);
    }
}
