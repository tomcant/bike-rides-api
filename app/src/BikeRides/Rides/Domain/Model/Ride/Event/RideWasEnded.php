<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride\Event;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Helpers\AggregateVersion;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\Foundation\Json;

final readonly class RideWasEnded implements AggregateEvent
{
    public const string EVENT_NAME = 'ride.ended';

    public function __construct(
        public AggregateVersion $aggregateVersion,
        public RideId $aggregateId,
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
            'occurredAt' => \datetime_timestamp($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = Json::decode($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RideId::fromString($event['aggregateId']),
            new \DateTimeImmutable($event['occurredAt']),
        );
    }
}
