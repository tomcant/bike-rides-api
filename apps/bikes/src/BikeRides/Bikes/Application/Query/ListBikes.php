<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;

final readonly class ListBikes
{
    public function __construct(private BikeRepository $repository)
    {
    }

    /**
     * @return list<array{
     *   bike_id: string,
     *   is_active: bool,
     * }>
     */
    public function query(): array
    {
        return \array_map(
            static fn (Bike $bike) => [
                'bike_id' => $bike->bikeId->toString(),
                'is_active' => $bike->isActive,
            ],
            $this->repository->list(),
        );
    }
}
