<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocation;
use App\BikeRides\Bikes\Infrastructure\PostgresBikeLocationRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Infrastructure\PostgresTestCase;

final class PostgresBikeLocationRepositoryTest extends PostgresTestCase
{
    private PostgresBikeLocationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresBikeLocationRepository($this->connection);
    }

    public function test_it_stores_a_bike_location(): void
    {
        $bikeLocation = new BikeLocation(
            bikeId: BikeId::generate(),
            location: new Location(0, 0),
            locatedAt: new \DateTimeImmutable('now'),
        );

        $this->repository->store($bikeLocation);

        $bikeLocations = $this->repository->getBetweenForBikeId(
            new \DateTimeImmutable('-1 minute'),
            new \DateTimeImmutable('+1 minute'),
            $bikeLocation->bikeId,
        );

        self::assertContainsEquals($bikeLocation, $bikeLocations);
    }

    public function test_it_lists_bike_locations_between_timestamps(): void
    {
        $bikeId = BikeId::generate();

        $this->repository->store(
            $bikeLocation1 = new BikeLocation(
                bikeId: $bikeId,
                location: new Location(0, 0),
                locatedAt: new \DateTimeImmutable('-5 minutes'),
            ),
        );
        $this->repository->store(
            $bikeLocation2 = new BikeLocation(
                bikeId: $bikeId,
                location: new Location(0, 0),
                locatedAt: new \DateTimeImmutable('-3 minutes'),
            ),
        );
        $this->repository->store(
            new BikeLocation(
                bikeId: $bikeId,
                location: new Location(0, 0),
                locatedAt: new \DateTimeImmutable('-1 minute'),
            ),
        );

        $bikeLocations = $this->repository->getBetweenForBikeId(
            new \DateTimeImmutable('-6 minutes'),
            new \DateTimeImmutable('-2 minutes'),
            $bikeId,
        );

        self::assertCount(2, $bikeLocations);
        self::assertContainsEquals($bikeLocation1, $bikeLocations);
        self::assertContainsEquals($bikeLocation2, $bikeLocations);
    }
}
