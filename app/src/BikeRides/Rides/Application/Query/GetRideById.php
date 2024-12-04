<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Projection\Ride\RideNotFound;
use App\BikeRides\Rides\Domain\Projection\Ride\RideProjectionRepository;

final readonly class GetRideById
{
    public function __construct(private RideProjectionRepository $repository)
    {
    }

    public function query(string $rideId): ?array
    {
        try {
            $ride = $this->repository->getById($rideId);
        } catch (RideNotFound) {
            return null;
        }

        return [
            'ride_id' => $ride->rideId,
            'rider_id' => $ride->riderId,
            'bike_id' => $ride->bikeId,
            'started_at' => $ride->startedAt,
            'ended_at' => $ride->endedAt,
        ];
    }
}
