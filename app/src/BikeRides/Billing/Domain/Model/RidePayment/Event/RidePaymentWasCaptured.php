<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment\Event;

use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Helpers\AggregateVersion;
use App\Foundation\Json;

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
            'occurredAt' => \datetime_timestamp($this->occurredAt),
        ]);
    }

    public static function deserialize(string $serialized): self
    {
        $event = Json::decode($serialized);

        return new self(
            AggregateVersion::fromInt($event['aggregateVersion']),
            RidePaymentId::fromString($event['aggregateId']),
            ExternalPaymentRef::fromString($event['externalPaymentRef']),
            new \DateTimeImmutable($event['occurredAt']),
        );
    }
}
