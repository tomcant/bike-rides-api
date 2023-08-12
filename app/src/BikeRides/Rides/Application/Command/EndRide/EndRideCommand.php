<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\EndRide;

use App\BikeRides\Rides\Domain\Model\Shared\RideId;
use App\BikeRides\Shared\Application\Command\Command;

final readonly class EndRideCommand implements Command
{
    public RideId $rideId;

    public function __construct(string $rideId)
    {
        $this->rideId = RideId::fromString($rideId);
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'rideId' => $this->rideId->toString(),
        ]);
    }
}
