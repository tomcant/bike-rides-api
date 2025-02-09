<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Query;

use App\BikeRides\Rides\Application\Query\ListBikes;
use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\Tests\BikeRides\Rides\Doubles\InMemoryBikeRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class ListBikesTest extends QueryTestCase
{
    private ListBikes $query;
    private InMemoryBikeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new ListBikes(
            $this->repository = new InMemoryBikeRepository(),
        );
    }

    public function test_it_can_list_bikes(): void
    {
        $this->repository->store(
            $bike1 = new Bike(BikeId::fromInt(1), new Location(0, 0)),
        );
        $this->repository->store(
            $bike2 = new Bike(BikeId::fromInt(2), new Location(0, 0)),
        );

        $bikes = $this->query->query();

        self::assertCount(2, $bikes);
        self::assertSame($bike1->bikeId->toInt(), $bikes[0]['bike_id']);
        self::assertSame($bike2->bikeId->toInt(), $bikes[1]['bike_id']);
        self::assertEquals($bike1->location->toArray(), $bikes[0]['location']);
        self::assertEquals($bike2->location->toArray(), $bikes[1]['location']);
    }
}
