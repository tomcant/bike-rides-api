<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Query;

use App\BikeRides\Rides\Application\Query\GetBikeById;
use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\Tests\BikeRides\Rides\Doubles\InMemoryBikeRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class GetBikeByIdTest extends QueryTestCase
{
    private GetBikeById $query;
    private InMemoryBikeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new GetBikeById(
            $this->repository = new InMemoryBikeRepository(),
        );
    }

    public function test_it_can_get_a_bike_by_id(): void
    {
        $this->repository->store(
            new Bike(
                $bikeId = BikeId::generate(),
                $location = new Location(0, 0),
            ),
        );

        $bike = $this->query->query($bikeId->toString());

        self::assertSame($bikeId->toString(), $bike['bike_id']);
        self::assertEquals($location->toArray(), $bike['location']);
    }

    public function test_no_bike_is_found_when_given_an_unknown_bike_id(): void
    {
        $bikeId = BikeId::generate();

        $bike = $this->query->query($bikeId->toString());

        self::assertNull($bike);
    }
}
