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
     *   bike_id: string,
     *   location: array{
     *     latitude: float,
     *     longitude: float,
     *   },
     * }
     */
    public function query(string $bikeId): ?array
    {
        try {
            $bike = $this->repository->getById(BikeId::fromString($bikeId));
        } catch (BikeNotFound) {
            return null;
        }

        return [
            'bike_id' => $bike->bikeId->toString(),
            'location' => $bike->location->toArray(),
        ];
    }
}
