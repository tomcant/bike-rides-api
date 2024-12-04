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

    public function query(): array
    {
        return \array_map(
            static fn (Bike $bike) => [
                'bike_id' => $bike->bikeId->toString(),
                'location' => $bike->location?->toArray(),
            ],
            $this->repository->list(),
        );
    }
}
