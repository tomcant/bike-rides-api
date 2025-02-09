<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final readonly class GetBikeById
{
    public function __construct(
        private BikeRepository $bikeRepository,
        private TrackingEventRepository $trackingEventRepository,
    ) {
    }

    /**
     * @return ?array{
     *   bike_id: int,
     *   is_active: bool,
     *   location: array{
     *     latitude: float,
     *     longitude: float,
     *   }|null,
     * }
     */
    public function query(int $bikeId): ?array
    {
        try {
            $bike = $this->bikeRepository->getById(BikeId::fromInt($bikeId));
        } catch (BikeNotFound) {
            return null;
        }

        return [
            'bike_id' => $bike->bikeId->toInt(),
            'is_active' => $bike->isActive,
            'location' => $this->trackingEventRepository->getLastEventForBikeId($bike->bikeId)?->location->toArray(),
        ];
    }
}
