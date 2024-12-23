<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

final readonly class RideId
{
    public function __construct(private string $rideId)
    {
    }

    public static function fromString(string $rideId): self
    {
        return new self($rideId);
    }

    public function toString(): string
    {
        return $this->rideId;
    }
}
