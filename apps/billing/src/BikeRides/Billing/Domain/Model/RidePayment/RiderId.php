<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

final readonly class RiderId
{
    public function __construct(private string $riderId)
    {
    }

    public static function fromString(string $riderId): self
    {
        return new self($riderId);
    }

    public function toString(): string
    {
        return $this->riderId;
    }
}
