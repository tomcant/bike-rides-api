<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;

final readonly class ListBikes
{
    public function __construct(
        private BikeRepository $bikeRepository,
        private TrackingEventRepository $trackingEventRepository,
    ) {
    }

    /**
     * @return list<array{
     *   bike_id: string,
     *   is_active: bool,
     *   location: array{
     *     latitude: float,
     *     longitude: float,
     *   }|null,
     * }>
     */
    public function query(): array
    {
        return \array_map(
            fn (Bike $bike) => [
                'bike_id' => $bike->bikeId->toString(),
                'is_active' => $bike->isActive,
                'location' => $this->trackingEventRepository->getLastEventForBikeId($bike->bikeId)?->location->toArray(),
            ],
            $this->bikeRepository->list(),
        );
    }
}
