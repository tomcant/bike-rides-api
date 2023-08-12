<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use App\BikeRides\Shared\Domain\Model\RideDuration;

final readonly class RideDetails
{
    public function __construct(public RideDuration $duration)
    {
    }

    public function toArray(): array
    {
        return ['duration' => $this->duration->toArray()];
    }

    public static function fromArray(array $rideDetails): self
    {
        return new self(RideDuration::fromArray($rideDetails['duration']));
    }
}
