<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride\Event;

use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateEventFactory;

final class RideEventFactory implements AggregateEventFactory
{
    public function fromSerialized(string $eventName, string $eventPayload): AggregateEvent
    {
        return match ($eventName) {
            RideWasStarted::EVENT_NAME => RideWasStarted::deserialize($eventPayload),
            RideWasEnded::EVENT_NAME => RideWasEnded::deserialize($eventPayload),
            RideWasSummarised::EVENT_NAME => RideWasSummarised::deserialize($eventPayload),
            default => throw new \DomainException(\sprintf("Unable to build event '%s'", $eventName)),
        };
    }
}
