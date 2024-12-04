<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use App\BikeRides\Shared\Domain\Helpers\Aggregate;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\Foundation\Clock\Clock;

final class RidePayment extends Aggregate
{
    public const AGGREGATE_NAME = 'ride_payment';
    private RidePaymentId $ridePaymentId;
    private RideId $rideId;
    private RidePrice $ridePrice;
    private ?ExternalPaymentRef $externalPaymentRef;

    public function getAggregateName(): AggregateName
    {
        return AggregateName::fromString(self::AGGREGATE_NAME);
    }

    public function getAggregateId(): RidePaymentId
    {
        return $this->ridePaymentId;
    }

    public function getRideId(): RideId
    {
        return $this->rideId;
    }

    public function getRidePrice(): RidePrice
    {
        return $this->ridePrice;
    }

    public function getExternalPaymentRef(): ?ExternalPaymentRef
    {
        return $this->externalPaymentRef;
    }

    public function isCaptured(): bool
    {
        return null !== $this->externalPaymentRef;
    }

    public static function initiate(
        RidePaymentId $ridePaymentId,
        RideId $rideId,
        RideDetailsFetcher $rideDetailsFetcher,
        RidePaymentDuplicateChecker $duplicateChecker,
    ): self {
        if ($duplicateChecker->isDuplicate($rideId)) {
            throw new RidePaymentAlreadyExists($rideId);
        }

        $ridePayment = new self();
        $rideDetails = $rideDetailsFetcher->fetch($rideId);
        $ridePrice = (new RidePriceCalculator())->calculatePrice($rideDetails->duration);

        $ridePayment->raise(
            new Event\RidePaymentWasInitiated(
                $ridePayment->getAggregateVersion(),
                $ridePaymentId,
                $rideId,
                $ridePrice,
                Clock::now(),
            ),
        );

        return $ridePayment;
    }

    public function capture(RidePaymentGateway $ridePaymentGateway): void
    {
        if ($this->isCaptured()) {
            throw new RidePaymentAlreadyCaptured($this->ridePaymentId);
        }

        $externalPaymentRef = $ridePaymentGateway->capture($this->ridePaymentId);

        $this->raise(
            new Event\RidePaymentWasCaptured(
                $this->getAggregateVersion(),
                $this->ridePaymentId,
                $externalPaymentRef,
                Clock::now(),
            ),
        );
    }

    protected function applyRidePaymentWasInitiated(Event\RidePaymentWasInitiated $event): void
    {
        $this->ridePaymentId = $event->getAggregateId();
        $this->rideId = $event->rideId;
        $this->ridePrice = $event->ridePrice;
        $this->externalPaymentRef = null;
    }

    protected function applyRidePaymentWasCaptured(Event\RidePaymentWasCaptured $event): void
    {
        $this->externalPaymentRef = $event->externalPaymentRef;
    }
}
