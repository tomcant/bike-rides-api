<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Projection\RidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\Event;
use BikeRides\Foundation\Domain\AggregateEventsSubscriber;

final class RidePaymentProjector extends AggregateEventsSubscriber
{
    public function __construct(private readonly RidePaymentProjectionRepository $repository)
    {
    }

    protected function handleRidePaymentWasInitiated(Event\RidePaymentWasInitiated $event): void
    {
        $ridePayment = RidePayment::initiate(
            $event->getAggregateId()->toString(),
            $event->rideId->toString(),
            $event->ridePrice->totalPrice,
            $event->ridePrice->pricePerMinute,
            $event->occurredAt,
        );

        $this->repository->store($ridePayment);
    }

    protected function handleRidePaymentWasCaptured(Event\RidePaymentWasCaptured $event): void
    {
        $ridePayment = $this->repository->getById($event->getAggregateId()->toString());

        $ridePayment->capture($event->occurredAt, $event->externalPaymentRef->toString());

        $this->repository->store($ridePayment);
    }
}
