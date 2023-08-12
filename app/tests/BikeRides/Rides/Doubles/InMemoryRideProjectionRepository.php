<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Projection\Ride\Ride;
use App\BikeRides\Rides\Domain\Projection\Ride\RideNotFound;
use App\BikeRides\Rides\Domain\Projection\Ride\RideProjectionRepository;

final class InMemoryRideProjectionRepository implements RideProjectionRepository
{
    private array $rides = [];

    public function store(Ride $ride): void
    {
        $this->rides[$ride->rideId] = $ride;
    }

    public function getById(string $rideId): Ride
    {
        return $this->rides[$rideId] ?? throw new RideNotFound($rideId);
    }
}
