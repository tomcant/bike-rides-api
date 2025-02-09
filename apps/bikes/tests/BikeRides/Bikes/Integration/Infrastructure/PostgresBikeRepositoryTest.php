<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Integration\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Infrastructure\PostgresBikeRepository;
use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class PostgresBikeRepositoryTest extends PostgresTestCase
{
    private PostgresBikeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresBikeRepository($this->connection);
    }

    public function test_it_stores_a_bike(): void
    {
        $correlationId = CorrelationId::generate();
        $bike = Bike::register($correlationId);

        $this->repository->store($bike);

        $storedBike = $this->repository->getByRegistrationCorrelationId($correlationId);
        self::assertNotNull($storedBike->bikeId);
    }

    public function test_it_updates_a_stored_bike(): void
    {
        $correlationId = CorrelationId::generate();
        $this->repository->store(Bike::register($correlationId));
        $storedBike = $this->repository->getByRegistrationCorrelationId($correlationId);
        $storedBike->activate(new Location(0, 0));

        $this->repository->store($storedBike);

        $updatedBike = $this->repository->getById($storedBike->bikeId);
        self::assertTrue($updatedBike->isActive);
    }

    public function test_it_cannot_get_a_bike_by_an_unknown_bike_id(): void
    {
        self::expectException(BikeNotFound::class);

        $this->repository->getById(BikeId::fromInt(1));
    }

    public function test_it_lists_bikes(): void
    {
        $correlationId1 = CorrelationId::generate();
        $correlationId2 = CorrelationId::generate();
        $this->repository->store(Bike::register($correlationId1));
        $this->repository->store(Bike::register($correlationId2));
        $bike1 = $this->repository->getByRegistrationCorrelationId($correlationId1);
        $bike2 = $this->repository->getByRegistrationCorrelationId($correlationId2);

        $bikes = $this->repository->list();

        self::assertCount(2, $bikes);
        self::assertContainsEquals($bike1, $bikes);
        self::assertContainsEquals($bike2, $bikes);
    }
}
