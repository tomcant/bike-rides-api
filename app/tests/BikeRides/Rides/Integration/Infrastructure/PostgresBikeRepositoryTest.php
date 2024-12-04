<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Integration\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Infrastructure\PostgresBikeRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Integration\Infrastructure\PostgresTestCase;

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
        $bike = new Bike(BikeId::generate(), new Location(0, 0));

        $this->repository->store($bike);

        self::assertEquals($bike, $this->repository->getById($bike->bikeId));
    }

    public function test_it_stores_an_updated_bike(): void
    {
        $bike = new Bike(BikeId::generate(), new Location(0, 0));

        $this->repository->store($bike);

        $bike->locate(new Location(1, 1));

        $this->repository->store($bike);

        self::assertEquals($bike, $this->repository->getById($bike->bikeId));
    }

    public function test_unable_to_get_by_unknown_id(): void
    {
        self::expectException(BikeNotFound::class);

        $this->repository->getById(BikeId::generate());
    }

    public function test_it_lists_bikes(): void
    {
        $this->repository->store($bike1 = new Bike(BikeId::generate(), new Location(0, 0)));
        $this->repository->store($bike2 = new Bike(BikeId::generate(), new Location(1, 1)));

        $bikes = $this->repository->list();

        self::assertCount(2, $bikes);
        self::assertContainsEquals($bike1, $bikes);
        self::assertContainsEquals($bike2, $bikes);
    }
}
