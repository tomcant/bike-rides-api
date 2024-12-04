<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride\Event;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Helpers\AggregateVersion;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;

final readonly class RideWasStarted implements AggregateEvent
{
    public const EVENT_NAME = 'ride.started';

    public function __construct(
        public AggregateVersion $aggregateVersion,
        public RideId $aggregateId,
        public RiderId $riderId,
        public BikeId $bikeId,
        public \DateTimeImmutable $occurredAt,
    ) {
    }

    public function getEventName(): string
    {
        return self::EVENT_NAME;
    }

    public function getAggregateName(): AggregateName
    {
        return AggregateName::fromString(Ride::AGGREGATE_NAME);
    }

    public function getAggregateVersion(): AggregateVersion
    {
        return $this->aggregateVersion;
    }

    public function getAggregateId(): RideId
    {
        return $this->aggregateId;
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'aggregateVersion' => $this->aggregateVersion->toInt(),
            'aggregateId' => $this->aggregateId->toString(),
            'riderId' => $this->riderId->toString(),
            'bikeId' => $this->bikeId->toString(),
            'occurredAt' => \datetime_timestamp($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = \json_decode_array($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RideId::fromString($event['aggregateId']),
            RiderId::fromString($event['riderId']),
            BikeId::fromString($event['bikeId']),
            new \DateTimeImmutable($event['occurredAt']),
        );
    }
}
