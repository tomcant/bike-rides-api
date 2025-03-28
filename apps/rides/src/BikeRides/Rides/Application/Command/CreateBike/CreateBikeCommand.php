<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\CreateBike;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final readonly class CreateBikeCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(
        int $bikeId,
        public Location $location,
    ) {
        $this->bikeId = BikeId::fromInt($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId->toInt(),
            'location' => $this->location->toArray(),
        ]);
    }
}
