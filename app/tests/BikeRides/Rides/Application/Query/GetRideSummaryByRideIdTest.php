<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Application\Query\GetRideSummaryByRideId;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Rides\Domain\Model\Ride\Summary;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryProjector;
use App\BikeRides\Shared\Domain\Model\RideDuration;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\Foundation\Location;
use App\Tests\BikeRides\Rides\Doubles\InMemoryRideSummaryProjectionRepository;

final class GetRideSummaryByRideIdTest extends QueryTestCase
{
    private GetRideSummaryByRideId $query;
    private RideSummaryProjector $projector;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryRideSummaryProjectionRepository();
        $this->query = new GetRideSummaryByRideId($repository);
        $this->projector = new RideSummaryProjector($repository);
    }

    public function test_it_can_get_a_ride_summary_by_ride_id(): void
    {
        $rideId = RideId::generate();

        $rideSummary = new Summary(
            $duration = RideDuration::fromStartAndEnd(
                $startedAt = new \DateTimeImmutable('now'),
                endedAt: new \DateTimeImmutable('+1 minute'),
            ),
            $route = new Route([$startedAt->getTimestamp() => new Location(0, 0)]),
        );

        $this->summariseRide($rideId, $rideSummary);
        $this->runProjector($this->projector);

        $summary = $this->query->query($rideId->toString());

        self::assertSame($rideId->toString(), $summary['ride_id']);
        self::assertEquals($route->toArray(), $summary['route']);

        self::assertEquals(
            [
                'started_at' => $duration->startedAt,
                'ended_at' => $duration->endedAt,
                'minutes' => $duration->minutes,
            ],
            $summary['duration'],
        );
    }

    public function test_no_ride_summary_is_found_when_given_an_unknown_ride_id(): void
    {
        $rideId = RideId::generate();

        $summary = $this->query->query($rideId->toString());

        self::assertNull($summary);
    }
}
