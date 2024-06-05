<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;

final readonly class GetBikeById
{
    public function __construct(private BikeRepository $repository)
    {
    }

    public function query(string $bikeId): ?array
    {
        try {
            $bike = $this->repository->getById(BikeId::fromString($bikeId));
        } catch (BikeNotFound) {
            return null;
        }

        return [
            'bike_id' => $bike->bikeId->toString(),
            'location' => $bike->location?->toArray(),
        ];
    }
}
