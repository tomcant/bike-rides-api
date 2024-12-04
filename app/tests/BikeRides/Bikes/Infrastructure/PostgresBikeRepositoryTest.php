<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Infrastructure\PostgresBikeRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Infrastructure\PostgresTestCase;

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
        $bike = new Bike(BikeId::generate(), location: null, isActive: false);

        $this->repository->store($bike);

        self::assertEquals($bike, $this->repository->getById($bike->bikeId));
    }

    public function test_it_stores_an_updated_bike(): void
    {
        $bike = new Bike(BikeId::generate(), location: null, isActive: false);

        $this->repository->store($bike);

        $bike->activate(new Location(1, 1));

        $this->repository->store($bike);

        self::assertEquals($bike, $this->repository->getById($bike->bikeId));
    }

    public function test_unable_to_get_by_unknown_id(): void
    {
        self::expectException(BikeNotFound::class);

        $this->repository->getById(BikeId::generate());
    }
}
