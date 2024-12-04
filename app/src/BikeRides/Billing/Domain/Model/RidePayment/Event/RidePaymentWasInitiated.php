<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment\Event;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePrice;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Helpers\AggregateVersion;

final readonly class RidePaymentWasInitiated implements AggregateEvent
{
    public const EVENT_NAME = 'ride_payment.initiated';

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
        return \json_encode_array([
            'aggregateVersion' => $this->aggregateVersion->toInt(),
            'aggregateId' => $this->aggregateId->toString(),
            'rideId' => $this->rideId->toString(),
            'ridePrice' => $this->ridePrice->toArray(),
            'occurredAt' => \datetime_timestamp($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = \json_decode_array($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RidePaymentId::fromString($event['aggregateId']),
            RideId::fromString($event['rideId']),
            RidePrice::fromArray($event['ridePrice']),
            new \DateTimeImmutable($event['occurredAt']),
        );
    }
}
