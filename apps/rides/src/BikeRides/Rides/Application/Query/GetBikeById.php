<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final readonly class GetBikeById
{
    public function __construct(private BikeRepository $repository)
    {
    }

    /**
     * @return ?array{
     *   bike_id: int,
     *   location: array{
     *     latitude: float,
     *     longitude: float,
     *   },
     * }
     */
    public function query(int $bikeId): ?array
    {
        try {
            $bike = $this->repository->getById(BikeId::fromInt($bikeId));
        } catch (BikeNotFound) {
            return null;
        }

        return [
            'bike_id' => $bike->bikeId->toInt(),
            'location' => $bike->location->toArray(),
        ];
    }
}
