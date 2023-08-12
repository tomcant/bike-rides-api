<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use App\Foundation\Clock\Clock;

final class ClockStub extends Clock
{
    public function __construct(private \DateTimeImmutable $now = new \DateTimeImmutable())
    {
    }

    public function setNow(\DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    public function tick(): void
    {
        $this->now = $this->now->modify('+1 second');
    }

    protected function getNow(): \DateTimeImmutable
    {
        return $this->now;
    }
}
