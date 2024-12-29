<?php

declare(strict_types=1);

namespace BikeRides\SharedKernel\Domain\Event;

use App\BikeRides\Bikes\Domain\Model\Bike\UnknownDomainEventType;
use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\SharedKernel\Domain\Model\Location;
use CloudEvents\V1\CloudEventInterface;

final readonly class DomainEventFactory
{
    public static function fromCloudEvent(CloudEventInterface $cloudEvent): DomainEvent
    {
        $data = $cloudEvent->getData();

        return match ($cloudEvent->getType()) {
            'bike-rides.bike-activated.v1' => new BikeActivated($data['bikeId'], Location::fromArray($data['location'])),
            'bike-rides.ride-ended.v1' => new RideEnded($data['rideId'], $data['bikeId']),
            'bike-rides.ride-payment-initiated.v1' => new RidePaymentInitiated($data['ridePaymentId'], $data['rideId']),
            default => throw new UnknownDomainEventType($cloudEvent->getType()),
        };
    }
}
