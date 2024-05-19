<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use App\Foundation\Clock\Clock;

final class ClockStub extends Clock
{
    public function __construct(private \DateTimeImmutable $now = new \DateTimeImmutable('now'))
    {
    }

    public function setNow(\DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    protected function getNow(): \DateTimeImmutable
    {
        $now = $this->now;

        $this->now = $this->now->modify('+1 second');

        return $now;
    }
}
