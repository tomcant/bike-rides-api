<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

interface AggregateEventFactory
{
    /** @throws \DomainException */
    public function fromSerialized(string $eventName, string $eventPayload): AggregateEvent;
}
