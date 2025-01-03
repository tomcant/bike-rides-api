<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Query;

use App\BikeRides\Billing\Application\Query\ListRidePayments;
use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjector;
use App\Tests\BikeRides\Billing\Doubles\InMemoryRidePaymentProjectionRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class ListRidePaymentsTest extends QueryTestCase
{
    private ListRidePayments $query;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryRidePaymentProjectionRepository();
        $this->query = new ListRidePayments($repository);
        $this->useProjector(new RidePaymentProjector($repository));
    }

    public function test_it_can_list_ride_payments(): void
    {
        $this->initiateRidePayment(
            $ridePaymentId1 = RidePaymentId::generate(),
            $rideId1 = RideId::generate(),
            $ridePrice1 = self::buildRidePrice(durationInMinutes: 1),
            $initiatedAt1 = Clock::now(),
        );
        $this->initiateRidePayment(
            $ridePaymentId2 = RidePaymentId::generate(),
            $rideId2 = RideId::generate(),
            $ridePrice2 = self::buildRidePrice(durationInMinutes: 2),
            $initiatedAt2 = Clock::now(),
        );
        $this->captureRidePayment(
            $ridePaymentId1,
            ExternalPaymentRef::fromString('external_payment_ref_1'),
            $capturedAt1 = Clock::now(),
        );
        $this->captureRidePayment(
            $ridePaymentId2,
            ExternalPaymentRef::fromString('external_payment_ref_2'),
            $capturedAt2 = Clock::now(),
        );

        $ridePayments = $this->query->query();

        $ridePayment1 = $ridePayments[0];
        self::assertSame($ridePaymentId1->toString(), $ridePayment1['ride_payment_id']);
        self::assertSame($rideId1->toString(), $ridePayment1['ride_id']);
        self::assertEquals($ridePrice1->totalPrice, $ridePayment1['total_price']);
        self::assertEquals($ridePrice1->pricePerMinute, $ridePayment1['price_per_minute']);
        self::assertEquals($initiatedAt1, $ridePayment1['initiated_at']);
        self::assertEquals($capturedAt1, $ridePayment1['captured_at']);
        self::assertSame('external_payment_ref_1', $ridePayment1['external_payment_ref']);

        $ridePayment2 = $ridePayments[1];
        self::assertSame($ridePaymentId2->toString(), $ridePayment2['ride_payment_id']);
        self::assertSame($rideId2->toString(), $ridePayment2['ride_id']);
        self::assertEquals($ridePrice2->totalPrice, $ridePayment2['total_price']);
        self::assertEquals($ridePrice2->pricePerMinute, $ridePayment2['price_per_minute']);
        self::assertEquals($initiatedAt2, $ridePayment2['initiated_at']);
        self::assertEquals($capturedAt2, $ridePayment2['captured_at']);
        self::assertSame('external_payment_ref_2', $ridePayment2['external_payment_ref']);
    }
}
