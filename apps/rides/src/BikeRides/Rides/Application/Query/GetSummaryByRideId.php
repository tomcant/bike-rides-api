<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Model\Summary\SummaryNotFound;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryRepository;
use BikeRides\Foundation\Money\Money;
use BikeRides\SharedKernel\Domain\Model\RideId;

final readonly class GetSummaryByRideId
{
    public function __construct(private SummaryRepository $repository)
    {
    }

    /**
     * @return ?array{
     *   ride_id: string,
     *   duration: array{
     *     started_at: \DateTimeImmutable,
     *     ended_at: \DateTimeImmutable,
     *     minutes: int,
     *   },
     *   route: array<int, array{
     *     latitude: float,
     *     longitude: float,
     *   }>,
     *   price: null|Money
     * }
     */
    public function query(string $rideId): ?array
    {
        try {
            $summary = $this->repository->getByRideId(RideId::fromString($rideId));
        } catch (SummaryNotFound) {
            return null;
        }

        return [
            'ride_id' => $summary->rideId->toString(),
            'duration' => [
                'started_at' => $summary->duration->startedAt,
                'ended_at' => $summary->duration->endedAt,
                'minutes' => $summary->duration->minutes,
            ],
            'route' => $summary->route->toArray(),
            'price' => $summary->price,
        ];
    }
}
