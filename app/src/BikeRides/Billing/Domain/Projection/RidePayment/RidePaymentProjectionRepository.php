<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Projection\RidePayment;

interface RidePaymentProjectionRepository
{
    public function store(RidePayment $ridePayment): void;

    /** @throws RidePaymentNotFound */
    public function getById(string $ridePaymentId): RidePayment;

    /** @throws RidePaymentNotFound */
    public function getByRideId(string $rideId): RidePayment;
}
