<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Doubles;

use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocation;
use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocationRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;

final class InMemoryBikeLocationRepository implements BikeLocationRepository
{
    /** @var array<BikeLocation> */
    private array $bikeLocations;

    public function store(BikeLocation $bikeLocation): void
    {
        $this->bikeLocations[$bikeLocation->bikeId->toString()][] = $bikeLocation;
    }

    public function getBetweenForBikeId(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        BikeId $bikeId,
    ): array {
        return \array_filter(
            $this->bikeLocations[$bikeId->toString()] ?? [],
            static fn ($bikeLocation) => $from <= $bikeLocation->locatedAt && $bikeLocation->locatedAt <= $to,
        );
    }
}
