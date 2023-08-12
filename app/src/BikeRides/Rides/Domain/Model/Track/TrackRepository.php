<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Track;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;

interface TrackRepository
{
    public function store(Track $track): void;

    /** @return array<Track> */
    public function getBetweenForBikeId(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        BikeId $bikeId,
    ): array;
}
