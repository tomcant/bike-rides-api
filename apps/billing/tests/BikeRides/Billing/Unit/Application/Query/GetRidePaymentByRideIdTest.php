<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Query;

use App\BikeRides\Billing\Application\Query\GetRidePaymentByRideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjector;
use App\Tests\BikeRides\Billing\Doubles\InMemoryRidePaymentProjectionRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class GetRidePaymentByRideIdTest extends QueryTestCase
{
    private GetRidePaymentByRideId $query;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryRidePaymentProjectionRepository();
        $this->query = new GetRidePaymentByRideId($repository);
        $this->useProjector(new RidePaymentProjector($repository));
    }

    public function test_it_can_get_a_ride_payment_by_ride_id(): void
    {
        $this->initiateRidePayment(
            $ridePaymentId = RidePaymentId::generate(),
            $rideId = RideId::generate(),
            $ridePrice = self::buildRidePrice(durationInMinutes: 1),
            $initiatedAt = Clock::now(),
        );
        $this->captureRidePayment(
            $ridePaymentId,
            ExternalPaymentRef::fromString('external_payment_ref'),
            $capturedAt = Clock::now(),
        );

        $ridePayment = $this->query->query($rideId->toString());

        self::assertSame($ridePaymentId->toString(), $ridePayment['ride_payment_id']);
        self::assertSame($rideId->toString(), $ridePayment['ride_id']);
        self::assertEquals($ridePrice->totalPrice, $ridePayment['total_price']);
        self::assertEquals($ridePrice->pricePerMinute, $ridePayment['price_per_minute']);
        self::assertEquals($initiatedAt, $ridePayment['initiated_at']);
        self::assertEquals($capturedAt, $ridePayment['captured_at']);
        self::assertSame('external_payment_ref', $ridePayment['external_payment_ref']);
    }

    public function test_no_ride_payment_is_found_when_given_an_unknown_ride_id(): void
    {
        $rideId = RideId::generate();

        $ridePayment = $this->query->query($rideId->toString());

        self::assertNull($ridePayment);
    }
}
