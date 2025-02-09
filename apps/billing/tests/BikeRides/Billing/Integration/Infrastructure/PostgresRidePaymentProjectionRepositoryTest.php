<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Integration\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentNotFound;
use App\BikeRides\Billing\Infrastructure\PostgresRidePaymentProjectionRepository;
use BikeRides\Foundation\Clock\Clock;
use Money\Money;

final class PostgresRidePaymentProjectionRepositoryTest extends PostgresTestCase
{
    private PostgresRidePaymentProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresRidePaymentProjectionRepository($this->connection);
    }

    public function test_it_stores_a_ride_payment(): void
    {
        $ridePayment = new RidePayment(
            ridePaymentId: RidePaymentId::generate()->toString(),
            rideId: 'ride_id',
            totalPrice: Money::GBP(15_00),
            pricePerMinute: Money::GBP(25),
            initiatedAt: Clock::now(),
        );

        $this->repository->store($ridePayment);

        self::assertEquals($ridePayment, $this->repository->getById($ridePayment->ridePaymentId));
    }

    public function test_it_updates_a_stored_ride_payment(): void
    {
        $ridePayment = new RidePayment(
            ridePaymentId: RidePaymentId::generate()->toString(),
            rideId: 'ride_id',
            totalPrice: Money::GBP(15_00),
            pricePerMinute: Money::GBP(25),
            initiatedAt: Clock::now(),
        );
        $this->repository->store($ridePayment);
        $ridePayment->capture(Clock::now(), 'external_payment_ref');

        $this->repository->store($ridePayment);

        self::assertEquals($ridePayment, $this->repository->getById($ridePayment->ridePaymentId));
    }

    public function test_it_cannot_get_a_ride_payment_by_an_unknown_ride_payment_id(): void
    {
        self::expectException(RidePaymentNotFound::class);

        $this->repository->getById(RidePaymentId::generate()->toString());
    }

    public function test_it_lists_ride_payments(): void
    {
        $this->repository->store(
            $ridePayment1 = new RidePayment(
                ridePaymentId: RidePaymentId::generate()->toString(),
                rideId: 'ride_id_1',
                totalPrice: Money::GBP(15_00),
                pricePerMinute: Money::GBP(25),
                initiatedAt: Clock::now(),
            ),
        );
        $this->repository->store(
            $ridePayment2 = new RidePayment(
                ridePaymentId: RidePaymentId::generate()->toString(),
                rideId: 'ride_id_2',
                totalPrice: Money::GBP(15_00),
                pricePerMinute: Money::GBP(25),
                initiatedAt: Clock::now(),
            ),
        );

        $ridePayments = $this->repository->list();

        self::assertCount(2, $ridePayments);
        self::assertContainsEquals($ridePayment1, $ridePayments);
        self::assertContainsEquals($ridePayment2, $ridePayments);
    }
}
