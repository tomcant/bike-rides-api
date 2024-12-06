<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\LocateBike;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Json;
use App\Foundation\Location;

final readonly class LocateBikeCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(
        string $bikeId,
        public Location $location,
        public \DateTimeImmutable $locatedAt,
    ) {
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId->toString(),
            'location' => $this->location->toArray(),
            'locatedAt' => \datetime_timestamp($this->locatedAt),
        ]);
    }
}
