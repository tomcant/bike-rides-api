<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Track\Track;
use App\BikeRides\Rides\Domain\Model\Track\TrackRepository;

final class InMemoryTrackRepository implements TrackRepository
{
    /** @var array<string, array<Track>> */
    private array $tracks;

    public function store(Track $track): void
    {
        $this->tracks[$track->bikeId->toString()][] = $track;
    }

    public function getBetweenForBikeId(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        BikeId $bikeId,
    ): array {
        return \array_filter(
            $this->tracks[$bikeId->toString()] ?? [],
            static fn ($track) => $from <= $track->trackedAt && $track->trackedAt <= $to,
        );
    }
}
