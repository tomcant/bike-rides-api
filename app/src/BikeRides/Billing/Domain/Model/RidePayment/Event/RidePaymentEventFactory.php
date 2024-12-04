<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment\Event;

use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventFactory;

final class RidePaymentEventFactory implements AggregateEventFactory
{
    public function fromSerialized(string $eventName, string $eventPayload): AggregateEvent
    {
        return match ($eventName) {
            RidePaymentWasInitiated::EVENT_NAME => RidePaymentWasInitiated::deserialize($eventPayload),
            RidePaymentWasCaptured::EVENT_NAME => RidePaymentWasCaptured::deserialize($eventPayload),
            default => throw new \DomainException(\sprintf("Unable to build event '%s'", $eventName)),
        };
    }
}
