<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride\Event;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\Foundation\Domain\AggregateVersion;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Timestamp;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final readonly class RideWasStarted implements AggregateEvent
{
    public const string EVENT_NAME = 'ride.started';

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
        return Json::encode([
            'aggregateVersion' => $this->aggregateVersion->toInt(),
            'aggregateId' => $this->aggregateId->toString(),
            'riderId' => $this->riderId->toString(),
            'bikeId' => $this->bikeId->toString(),
            'occurredAt' => Timestamp::format($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = Json::decode($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RideId::fromString($event['aggregateId']),
            RiderId::fromString($event['riderId']),
            BikeId::fromString($event['bikeId']),
            Timestamp::from($event['occurredAt']),
        );
    }
}
