<?php declare(strict_types=1);

namespace App\Foundation\Clock;

abstract class Clock
{
    private static ?self $instance = null;

    public static function useClock(self $clock): void
    {
        self::$instance = $clock;
    }

    public static function now(): \DateTimeImmutable
    {
        return (self::$instance ??= new NativeClock())->getNow();
    }

    abstract protected function getNow(): \DateTimeImmutable;
}
