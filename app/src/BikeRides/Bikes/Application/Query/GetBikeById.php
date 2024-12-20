<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;

final readonly class GetBikeById
{
    public function __construct(private BikeRepository $repository)
    {
    }

    /**
     * @return ?array{
     *   bike_id: string,
     *   is_active: bool,
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
            'is_active' => $bike->isActive,
        ];
    }
}
