<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Query;

use App\BikeRides\Bikes\Application\Query\GetBikeById;
use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Bikes\Unit\Doubles\InMemoryBikeRepository;
use PHPUnit\Framework\TestCase;

final class GetBikeByIdTest extends TestCase
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
        $bike = Bike::register($bikeId = BikeId::generate());
        $bike->activate($location = new Location(0, 0));
        $this->repository->store($bike);

        $bike = $this->query->query($bikeId->toString());

        self::assertSame($bikeId->toString(), $bike['bike_id']);
        self::assertEquals($location->toArray(), $bike['location']);
        self::assertTrue($bike['is_active']);
    }

    public function test_no_bike_is_found_when_given_an_unknown_bike_id(): void
    {
        $bikeId = BikeId::generate();

        $bike = $this->query->query($bikeId->toString());

        self::assertNull($bike);
    }
}
