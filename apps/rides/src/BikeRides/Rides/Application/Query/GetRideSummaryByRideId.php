<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryNotFound;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryProjectionRepository;

final readonly class GetRideSummaryByRideId
{
    public function __construct(private RideSummaryProjectionRepository $repository)
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
     * }
     */
    public function query(string $rideId): ?array
    {
        try {
            $summary = $this->repository->getByRideId($rideId);
        } catch (RideSummaryNotFound) {
            return null;
        }

        return [
            'ride_id' => $summary->rideId,
            'duration' => [
                'started_at' => $summary->duration->startedAt,
                'ended_at' => $summary->duration->endedAt,
                'minutes' => $summary->duration->minutes,
            ],
            'route' => $summary->route,
        ];
    }
}
