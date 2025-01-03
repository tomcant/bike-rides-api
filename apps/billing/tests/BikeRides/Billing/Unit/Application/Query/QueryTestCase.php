<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Query;

use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentWasCaptured;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentWasInitiated;
use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePrice;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\Foundation\Domain\AggregateEventsSubscriber;
use BikeRides\Foundation\Domain\AggregateVersion;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Money\Money;
use PHPUnit\Framework\TestCase;

abstract class QueryTestCase extends TestCase
{
    private AggregateVersion $version;
    private AggregateEventsSubscriber $projector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->version = AggregateVersion::zero();
    }

    final protected function useProjector(AggregateEventsSubscriber $projector): void
    {
        $this->projector = $projector;
    }

    protected function initiateRidePayment(RidePaymentId $ridePaymentId, RideId $rideId, RidePrice $ridePrice, \DateTimeImmutable $initiatedAt): void
    {
        $this->projectEvent(new RidePaymentWasInitiated($this->version, $ridePaymentId, $rideId, $ridePrice, $initiatedAt));
    }

    protected function captureRidePayment(RidePaymentId $ridePaymentId, ExternalPaymentRef $externalPaymentRef, \DateTimeImmutable $capturedAt): void
    {
        $this->projectEvent(new RidePaymentWasCaptured($this->version, $ridePaymentId, $externalPaymentRef, $capturedAt));
    }

    protected static function buildRidePrice(int $durationInMinutes): RidePrice
    {
        $priceInPencePerMinute = 25;

        $rideDuration = RideDuration::fromStartAndEnd(
            ($endedAt = Clock::now())->modify("-{$durationInMinutes} minutes"),
            $endedAt,
        );

        return new RidePrice(
            totalPrice: Money::GBP($durationInMinutes * $priceInPencePerMinute),
            pricePerMinute: Money::GBP($priceInPencePerMinute),
            rideDuration: $rideDuration,
        );
    }

    private function projectEvent(AggregateEvent $event): void
    {
        ($this->projector)(new AggregateEvents([$event]));
        $this->version = $this->version->next();
    }
}
