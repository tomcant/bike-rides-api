<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Projection\RidePayment;

interface RidePaymentProjectionRepository
{
    public function store(RidePayment $ridePayment): void;

    /** @throws RidePaymentNotFound */
    public function getById(string $ridePaymentId): RidePayment;

    /** @return array<RidePayment> */
    public function listByRideId(string $rideId): array;
}
