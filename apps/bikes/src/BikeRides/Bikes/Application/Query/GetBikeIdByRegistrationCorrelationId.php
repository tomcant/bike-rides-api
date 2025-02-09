<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Domain\CorrelationId;

final readonly class GetBikeIdByRegistrationCorrelationId
{
    public function __construct(
        private BikeRepository $bikeRepository,
    ) {
    }

    public function query(string $correlationId): ?int
    {
        try {
            $bike = $this->bikeRepository->getByRegistrationCorrelationId(CorrelationId::fromString($correlationId));
        } catch (BikeNotFound) {
            return null;
        }

        return $bike->bikeId->toInt();
    }
}
