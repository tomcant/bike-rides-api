<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Doubles;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentNotFound;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;

final class InMemoryRidePaymentProjectionRepository implements RidePaymentProjectionRepository
{
    /** @var array<string, RidePayment> */
    private array $ridePayments = [];

    public function store(RidePayment $ridePayment): void
    {
        $this->ridePayments[$ridePayment->ridePaymentId] = $ridePayment;
    }

    public function getById(string $ridePaymentId): RidePayment
    {
        return $this->ridePayments[$ridePaymentId] ?? throw RidePaymentNotFound::forRidePaymentId($ridePaymentId);
    }

    public function getByRideId(string $rideId): RidePayment
    {
        foreach ($this->ridePayments as $ridePayment) {
            if ($ridePayment->rideId === $rideId) {
                return $ridePayment;
            }
        }

        throw RidePaymentNotFound::forRideId($rideId);
    }
}
