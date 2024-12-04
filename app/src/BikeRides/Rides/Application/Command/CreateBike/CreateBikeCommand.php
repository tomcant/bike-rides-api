<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\CreateBike;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

final readonly class CreateBikeCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(
        string $bikeId,
        public Location $location,
    ) {
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'bikeId' => $this->bikeId->toString(),
            'location' => $this->location->toArray(),
        ]);
    }
}
