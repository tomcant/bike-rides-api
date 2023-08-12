<?php declare(strict_types=1);

namespace App\Foundation\Clock;

final class NativeClock extends Clock
{
    protected function getNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
