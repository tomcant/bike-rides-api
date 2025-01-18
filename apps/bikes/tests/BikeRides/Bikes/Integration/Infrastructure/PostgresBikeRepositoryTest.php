<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Integration\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Infrastructure\PostgresBikeRepository;
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
        $bike = new Bike(BikeId::generate(), isActive: false);

        $this->repository->store($bike);

        self::assertEquals($bike, $this->repository->getById($bike->bikeId));
    }

    public function test_it_stores_an_updated_bike(): void
    {
        $bike = new Bike(BikeId::generate(), isActive: false);

        $this->repository->store($bike);

        $bike->activate(new Location(0, 0));

        $this->repository->store($bike);

        self::assertEquals($bike, $this->repository->getById($bike->bikeId));
    }

    public function test_it_cannot_get_a_bike_by_an_unknown_bike_id(): void
    {
        self::expectException(BikeNotFound::class);

        $this->repository->getById(BikeId::generate());
    }

    public function test_it_lists_bikes(): void
    {
        $this->repository->store($bike1 = new Bike(BikeId::generate(), isActive: false));
        $this->repository->store($bike2 = new Bike(BikeId::generate(), isActive: true));

        $bikes = $this->repository->list();

        self::assertCount(2, $bikes);
        self::assertContainsEquals($bike1, $bikes);
        self::assertContainsEquals($bike2, $bikes);
    }
}
