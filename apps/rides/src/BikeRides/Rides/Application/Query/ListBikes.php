<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;

final readonly class ListBikes
{
    public function __construct(private BikeRepository $repository)
    {
    }

    /**
     * @return list<array{
     *   bike_id: int,
     *   location: array{
     *     latitude: float,
     *     longitude: float,
     *   },
     * }>
     */
    public function query(): array
    {
        return \array_map(
            static fn (Bike $bike) => [
                'bike_id' => $bike->bikeId->toInt(),
                'location' => $bike->location->toArray(),
            ],
            $this->repository->list(),
        );
    }
}
