<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Integration\Infrastructure;

use App\BikeRides\Rides\Domain\Projection\Ride\Ride;
use App\BikeRides\Rides\Domain\Projection\Ride\RideNotFound;
use App\BikeRides\Rides\Infrastructure\PostgresRideProjectionRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class PostgresRideProjectionRepositoryTest extends PostgresTestCase
{
    private PostgresRideProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresRideProjectionRepository($this->connection);
    }

    public function test_it_stores_a_ride(): void
    {
        $ride = new Ride(
            rideId: RideId::generate()->toString(),
            riderId: 'rider_id',
            bikeId: 1,
            startedAt: Clock::now(),
        );

        $this->repository->store($ride);

        self::assertEquals($ride, $this->repository->getById($ride->rideId));
    }

    public function test_it_updates_a_stored_ride(): void
    {
        $ride = new Ride(
            rideId: RideId::generate()->toString(),
            riderId: 'rider_id',
            bikeId: 1,
            startedAt: Clock::now(),
        );
        $this->repository->store($ride);
        $ride->end(Clock::now());

        $this->repository->store($ride);

        self::assertEquals($ride, $this->repository->getById($ride->rideId));
    }

    public function test_it_cannot_get_a_ride_by_an_unknown_ride_id(): void
    {
        self::expectException(RideNotFound::class);

        $this->repository->getById(RideId::generate()->toString());
    }
}
