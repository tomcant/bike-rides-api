<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

abstract readonly class DomainEvent
{
    public \DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new \DateTimeImmutable('now'); // Clock::now();
    }

    abstract public function serialize(): string;
}
