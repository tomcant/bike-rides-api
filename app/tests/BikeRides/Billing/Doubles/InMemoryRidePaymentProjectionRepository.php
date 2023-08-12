<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Doubles;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;

final class InMemoryRidePaymentProjectionRepository implements RidePaymentProjectionRepository
{
    private array $ridePayments = [];

    public function store(RidePayment $ridePayment): void
    {
        $this->ridePayments[$ridePayment->ridePaymentId] = $ridePayment;
    }

    public function listByRideId(string $rideId): array
    {
        return \array_values(
            \array_filter(
                $this->ridePayments,
                static fn (RidePayment $ridePayment) => $rideId === $ridePayment->rideId,
            ),
        );
    }
}
