<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\TrackBike;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Shared\Application\Command\Command;
use App\Foundation\Location;

final readonly class TrackBikeCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(
        string $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'bikeId' => $this->bikeId->toString(),
            'location' => $this->location->toArray(),
            'trackedAt' => \datetime_timestamp($this->trackedAt),
        ]);
    }
}
