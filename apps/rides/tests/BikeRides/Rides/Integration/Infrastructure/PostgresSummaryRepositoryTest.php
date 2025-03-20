<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Integration\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Summary\Route;
use App\BikeRides\Rides\Domain\Model\Summary\Summary;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryNotFound;
use App\BikeRides\Rides\Infrastructure\PostgresSummaryRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Money\Money;

final class PostgresSummaryRepositoryTest extends PostgresTestCase
{
    private PostgresSummaryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresSummaryRepository($this->connection);
    }

    public function test_it_stores_a_summary(): void
    {
        $summary = new Summary(
            rideId: RideId::generate(),
            duration: RideDuration::fromStartAndEnd(
                new \DateTimeImmutable('-1 minute'),
                Clock::now(),
            ),
            route: new Route(locations: []),
            price: null,
        );

        $this->repository->store($summary);

        self::assertEquals($summary, $this->repository->getByRideId($summary->rideId));
    }

    public function test_it_updates_a_stored_summary(): void
    {
        $summary = new Summary(
            rideId: RideId::generate(),
            duration: RideDuration::fromStartAndEnd(
                new \DateTimeImmutable('-1 minute'),
                Clock::now(),
            ),
            route: new Route(locations: []),
            price: null,
        );
        $this->repository->store($summary);
        $summary->price = Money::GBP(100);

        $this->repository->store($summary);

        self::assertEquals($summary, $this->repository->getByRideId($summary->rideId));
    }

    public function test_it_cannot_get_a_summary_by_an_unknown_ride_id(): void
    {
        self::expectException(SummaryNotFound::class);

        $this->repository->getByRideId(RideId::generate());
    }
}
