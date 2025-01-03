<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment\Event;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePrice;
use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\Foundation\Domain\AggregateVersion;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Timestamp;
use BikeRides\SharedKernel\Domain\Model\RideId;

final readonly class RidePaymentWasInitiated implements AggregateEvent
{
    public const string EVENT_NAME = 'ride_payment.initiated';

    public function __construct(
        public AggregateVersion $aggregateVersion,
        public RidePaymentId $aggregateId,
        public RideId $rideId,
        public RidePrice $ridePrice,
        public \DateTimeImmutable $occurredAt,
    ) {
    }

    public function getEventName(): string
    {
        return self::EVENT_NAME;
    }

    public function getAggregateName(): AggregateName
    {
        return AggregateName::fromString(RidePayment::AGGREGATE_NAME);
    }

    public function getAggregateVersion(): AggregateVersion
    {
        return $this->aggregateVersion;
    }

    public function getAggregateId(): RidePaymentId
    {
        return $this->aggregateId;
    }

    public function serialize(): string
    {
        return Json::encode([
            'aggregateVersion' => $this->aggregateVersion->toInt(),
            'aggregateId' => $this->aggregateId->toString(),
            'rideId' => $this->rideId->toString(),
            'ridePrice' => $this->ridePrice->toArray(),
            'occurredAt' => Timestamp::format($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = Json::decode($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RidePaymentId::fromString($event['aggregateId']),
            RideId::fromString($event['rideId']),
            RidePrice::fromArray($event['ridePrice']),
            Timestamp::from($event['occurredAt']),
        );
    }
}
