<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Query;

use App\BikeRides\Rides\Application\Query\GetSummaryByRideId;
use App\BikeRides\Rides\Domain\Model\Summary\Route;
use App\BikeRides\Rides\Domain\Model\Summary\Summary;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryRepository;
use App\Tests\BikeRides\Rides\Doubles\InMemorySummaryRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\Location;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use PHPUnit\Framework\TestCase;

final class GetSummaryByRideIdTest extends TestCase
{
    private GetSummaryByRideId $query;
    private SummaryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new GetSummaryByRideId(
            $this->repository = new InMemorySummaryRepository(),
        );
    }

    public function test_it_can_get_a_summary_by_ride_id(): void
    {
        $this->repository->store(
            new Summary(
                $rideId = RideId::generate(),
                $duration = RideDuration::fromStartAndEnd(($endedAt = Clock::now())->modify('-1 minute'), $endedAt),
                $route = new Route([$endedAt->getTimestamp() => new Location(0, 0)]),
            ),
        );

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

    public function test_no_summary_is_found_when_given_an_unknown_ride_id(): void
    {
        $rideId = RideId::generate();

        $summary = $this->query->query($rideId->toString());

        self::assertNull($summary);
    }
}
