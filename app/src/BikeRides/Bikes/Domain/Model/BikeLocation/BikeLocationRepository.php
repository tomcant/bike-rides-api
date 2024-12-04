<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\BikeLocation;

use App\BikeRides\Shared\Domain\Model\BikeId;

interface BikeLocationRepository
{
    public function store(BikeLocation $bikeLocation): void;

    /** @return list<BikeLocation> */
    public function getBetweenForBikeId(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        BikeId $bikeId,
    ): array;
}
