<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\StartRide;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;

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
        return \json_encode_array([
            'rideId' => $this->rideId->toString(),
            'riderId' => $this->riderId->toString(),
            'bikeId' => $this->bikeId->toString(),
        ]);
    }
}
