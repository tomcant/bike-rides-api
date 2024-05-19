<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Shared\RideId;
use App\BikeRides\Rides\Domain\Projection\Ride\Ride;
use App\BikeRides\Rides\Domain\Projection\Ride\RideNotFound;
use App\BikeRides\Rides\Infrastructure\PostgresRideProjectionRepository;
use App\Foundation\Clock\Clock;
use App\Tests\BikeRides\Shared\Infrastructure\PostgresTestCase;

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
            bikeId: BikeId::generate()->toString(),
            startedAt: Clock::now(),
        );

        $this->repository->store($ride);

        self::assertEquals($ride, $this->repository->getById($ride->rideId));
    }

    public function test_it_stores_an_updated_ride(): void
    {
        $ride = new Ride(
            rideId: RideId::generate()->toString(),
            riderId: 'rider_id',
            bikeId: BikeId::generate()->toString(),
            startedAt: Clock::now(),
        );

        $this->repository->store($ride);

        $ride->end(Clock::now());

        $this->repository->store($ride);

        self::assertEquals($ride, $this->repository->getById($ride->rideId));
    }

    public function test_unable_to_get_by_unknown_id(): void
    {
        self::expectException(RideNotFound::class);

        $this->repository->getById(RideId::generate()->toString());
    }
}
