<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Query;

use App\BikeRides\Rides\Application\Query\GetRideById;
use App\BikeRides\Rides\Domain\Projection\Ride\RideProjector;
use App\Tests\BikeRides\Rides\Doubles\InMemoryRideProjectionRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final class GetRideByIdTest extends QueryTestCase
{
    private GetRideById $query;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryRideProjectionRepository();
        $this->query = new GetRideById($repository);
        $this->useProjector(new RideProjector($repository));
    }

    public function test_it_can_get_a_ride_by_id(): void
    {
        $this->startRide(
            $rideId = RideId::generate(),
            $riderId = RiderId::fromString('rider_id'),
            $bikeId = BikeId::fromInt(1),
            $startedAt = Clock::now(),
        );

        $ride = $this->query->query($rideId->toString());

        self::assertSame($rideId->toString(), $ride['ride_id']);
        self::assertSame($riderId->toString(), $ride['rider_id']);
        self::assertSame($bikeId->toInt(), $ride['bike_id']);
        self::assertEquals($startedAt, $ride['started_at']);
        self::assertNull($ride['ended_at']);
    }

    public function test_no_ride_is_found_when_given_an_unknown_ride_id(): void
    {
        $rideId = RideId::generate();

        $ride = $this->query->query($rideId->toString());

        self::assertNull($ride);
    }
}
