<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Track\Track;
use App\BikeRides\Rides\Infrastructure\PostgresTrackRepository;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Infrastructure\PostgresTestCase;

final class PostgresTrackRepositoryTest extends PostgresTestCase
{
    private PostgresTrackRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresTrackRepository($this->connection);
    }

    public function test_it_stores_a_track(): void
    {
        $track = new Track(
            bikeId: BikeId::generate(),
            location: new Location(0, 0),
            trackedAt: new \DateTimeImmutable('now'),
        );

        $this->repository->store($track);

        $tracks = $this->repository->getBetweenForBikeId(
            new \DateTimeImmutable('-1 minute'),
            new \DateTimeImmutable('+1 minute'),
            $track->bikeId,
        );

        self::assertContainsEquals($track, $tracks);
    }

    public function test_it_lists_tracks_between_timestamps(): void
    {
        $bikeId = BikeId::generate();

        $this->repository->store(
            $track1 = new Track(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-5 minutes'),
            ),
        );
        $this->repository->store(
            $track2 = new Track(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-3 minutes'),
            ),
        );
        $this->repository->store(
            new Track(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-1 minute'),
            ),
        );

        $tracks = $this->repository->getBetweenForBikeId(
            new \DateTimeImmutable('-6 minutes'),
            new \DateTimeImmutable('-2 minutes'),
            $bikeId,
        );

        self::assertCount(2, $tracks);
        self::assertContainsEquals($track1, $tracks);
        self::assertContainsEquals($track2, $tracks);
    }
}
