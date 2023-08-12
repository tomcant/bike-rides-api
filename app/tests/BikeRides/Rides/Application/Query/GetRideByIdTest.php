<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Application\Query\GetRideById;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Shared\RideId;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use App\BikeRides\Rides\Domain\Projection\Ride\RideProjector;
use App\Tests\BikeRides\Rides\Doubles\InMemoryRideProjectionRepository;

final class GetRideByIdTest extends QueryTestCase
{
    private GetRideById $query;
    private RideProjector $projector;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryRideProjectionRepository();
        $this->query = new GetRideById($repository);
        $this->projector = new RideProjector($repository);
    }

    public function test_it_can_get_a_ride_by_id(): void
    {
        $this->startRide(
            $rideId = RideId::generate(),
            $riderId = RiderId::fromString('rider_id'),
            $bikeId = BikeId::generate(),
            $startedAt = new \DateTimeImmutable(),
        );
        $this->runProjector($this->projector);

        $ride = $this->query->query($rideId->toString());

        self::assertSame($rideId->toString(), $ride['ride_id']);
        self::assertSame($riderId->toString(), $ride['rider_id']);
        self::assertSame($bikeId->toString(), $ride['bike_id']);
        self::assertSame($startedAt, $ride['started_at']);
        self::assertNull($ride['ended_at']);
    }

    public function test_no_ride_is_found_when_given_an_unknown_ride_id(): void
    {
        $rideId = RideId::generate();

        $ride = $this->query->query($rideId->toString());

        self::assertNull($ride);
    }
}
