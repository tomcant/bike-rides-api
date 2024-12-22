<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\EndRide;

use App\BikeRides\Shared\Domain\Model\RideId;
use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;

final readonly class EndRideCommand implements Command
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
