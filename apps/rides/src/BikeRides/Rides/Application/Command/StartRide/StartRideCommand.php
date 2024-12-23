<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\StartRide;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final readonly class StartRideCommand implements Command
{
    public RideId $rideId;
    public RiderId $riderId;
    public BikeId $bikeId;

    public function __construct(string $rideId, string $riderId, string $bikeId)
    {
        $this->rideId = RideId::fromString($rideId);
        $this->riderId = RiderId::fromString($riderId);
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'rideId' => $this->rideId->toString(),
            'riderId' => $this->riderId->toString(),
            'bikeId' => $this->bikeId->toString(),
        ]);
    }
}
