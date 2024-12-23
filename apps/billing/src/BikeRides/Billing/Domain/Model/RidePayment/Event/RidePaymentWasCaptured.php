<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment\Event;

use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\Foundation\Domain\AggregateVersion;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Timestamp;

final readonly class RidePaymentWasCaptured implements AggregateEvent
{
    public const string EVENT_NAME = 'ride_payment.captured';

    public function __construct(
        public AggregateVersion $aggregateVersion,
        public RidePaymentId $aggregateId,
        public ExternalPaymentRef $externalPaymentRef,
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
            'externalPaymentRef' => $this->externalPaymentRef->toString(),
            'occurredAt' => Timestamp::format($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = Json::decode($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RidePaymentId::fromString($event['aggregateId']),
            ExternalPaymentRef::fromString($event['externalPaymentRef']),
            Timestamp::from($event['occurredAt']),
        );
    }
}
