<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Integration\Infrastructure;

use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummary;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryNotFound;
use App\BikeRides\Rides\Infrastructure\PostgresRideSummaryProjectionRepository;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class PostgresRideSummaryProjectionRepositoryTest extends PostgresTestCase
{
    private PostgresRideSummaryProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresRideSummaryProjectionRepository($this->connection);
    }

    public function test_it_stores_a_ride_summary(): void
    {
        $rideSummary = new RideSummary(
            rideId: RideId::generate()->toString(),
            duration: RideDuration::fromStartAndEnd(
                new \DateTimeImmutable('-1 minute'),
                new \DateTimeImmutable('now'),
            ),
            route: [],
        );

        $this->repository->store($rideSummary);

        self::assertEquals($rideSummary, $this->repository->getByRideId($rideSummary->rideId));
    }

    public function test_it_cannot_get_a_ride_summary_by_an_unknown_ride_id(): void
    {
        self::expectException(RideSummaryNotFound::class);

        $this->repository->getByRideId(RideId::generate()->toString());
    }
}
