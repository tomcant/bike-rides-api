<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

interface AggregateEventFactory
{
    /** @throws \DomainException */
    public function fromSerialized(string $eventName, string $eventPayload): AggregateEvent;
}
