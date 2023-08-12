<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

use App\Foundation\Clock\Clock;

abstract readonly class DomainEvent
{
    public \DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = Clock::now();
    }

    abstract public function serialize(): string;
}
