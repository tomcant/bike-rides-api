<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocation;
use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocationRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;

final readonly class ListBikeLocationsByBikeId
{
    public function __construct(private BikeLocationRepository $repository)
    {
    }

    public function query(\DateTimeImmutable $from, \DateTimeImmutable $to, string $bikeId): array
    {
        return \array_map(
            static fn (BikeLocation $bikeLocation) => [
                'location' => $bikeLocation->location->toArray(),
                'locatedAt' => $bikeLocation->locatedAt,
            ],
            $this->repository->getBetweenForBikeId($from, $to, BikeId::fromString($bikeId)),
        );
    }
}
