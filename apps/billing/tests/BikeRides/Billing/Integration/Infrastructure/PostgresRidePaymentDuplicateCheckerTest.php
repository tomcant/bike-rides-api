<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Integration\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Infrastructure\PostgresRidePaymentDuplicateChecker;
use App\BikeRides\Billing\Infrastructure\PostgresRidePaymentProjectionRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Money\Money;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class PostgresRidePaymentDuplicateCheckerTest extends PostgresTestCase
{
    private PostgresRidePaymentDuplicateChecker $duplicateChecker;
    private PostgresRidePaymentProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->duplicateChecker = new PostgresRidePaymentDuplicateChecker($this->connection);
        $this->repository = new PostgresRidePaymentProjectionRepository($this->connection);
    }

    public function test_it_can_detect_when_a_duplicate_ride_payment_exists(): void
    {
        $rideId = RideId::generate();
        $ridePayment = new RidePayment(
            ridePaymentId: RidePaymentId::generate()->toString(),
            rideId: $rideId->toString(),
            totalPrice: Money::GBP(15_00),
            pricePerMinute: Money::GBP(25),
            initiatedAt: Clock::now(),
        );
        $this->repository->store($ridePayment);

        $isDuplicate = $this->duplicateChecker->isDuplicate($rideId);

        self::assertTrue($isDuplicate);
    }

    public function test_it_can_detect_when_a_duplicate_ride_payment_does_not_exist(): void
    {
        $rideId = RideId::generate();

        $isDuplicate = $this->duplicateChecker->isDuplicate($rideId);

        self::assertFalse($isDuplicate);
    }
}
