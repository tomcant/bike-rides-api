<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

use BikeRides\Foundation\Clock\Clock;
use Symfony\Component\Uid\Uuid;

abstract readonly class DomainEvent
{
    public string $id;
    public \DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->occurredAt = Clock::now();
    }

    abstract public function type(): string;

    abstract public function version(): int;

    abstract public function serialize(): string;
}
