<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\SummariseRide;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\RideId;

final readonly class SummariseRideCommand implements Command
{
    public RideId $rideId;

    public function __construct(string $rideId)
    {
        $this->rideId = RideId::fromString($rideId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'rideId' => $this->rideId->toString(),
        ]);
    }
}
