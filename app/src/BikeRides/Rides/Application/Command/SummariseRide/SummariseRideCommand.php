<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\SummariseRide;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\RideId;

final readonly class SummariseRideCommand implements Command
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
