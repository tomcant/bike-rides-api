<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Integration\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentNotFound;
use App\BikeRides\Billing\Infrastructure\PostgresRidePaymentProjectionRepository;
use App\Foundation\Clock\Clock;
use App\Tests\BikeRides\Shared\Integration\Infrastructure\PostgresTestCase;
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

    public function test_it_stores_an_updated_ride_payment(): void
    {
        $ridePayment = new RidePayment(
            ridePaymentId: RidePaymentId::generate()->toString(),
            rideId: 'ride_id',
            totalPrice: Money::GBP(15_00),
            pricePerMinute: Money::GBP(25),
            initiatedAt: Clock::now(),
        );

        $this->repository->store($ridePayment);

        $ridePayment->capture(new \DateTimeImmutable('now'), 'external_payment_ref');

        $this->repository->store($ridePayment);

        self::assertEquals($ridePayment, $this->repository->getById($ridePayment->ridePaymentId));
    }

    public function test_it_cannot_get_a_ride_payment_by_an_unknown_ride_payment_id(): void
    {
        self::expectException(RidePaymentNotFound::class);

        $this->repository->getById(RidePaymentId::generate()->toString());
    }
}
