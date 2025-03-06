<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\SummariseRide;

use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use App\BikeRides\Rides\Domain\Model\Summary\Summary;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryNotFound;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;
use BikeRides\SharedKernel\Domain\Model\RideDuration;

final readonly class SummariseRideHandler implements CommandHandler
{
    public function __construct(
        private RideRepository $rideRepository,
        private SummaryRepository $summaryRepository,
        private RouteFetcher $routeFetcher,
    ) {
    }

    public function __invoke(SummariseRideCommand $command): void
    {
        $ride = $this->rideRepository->getById($command->rideId);

        if (!$ride->hasEnded()) {
            throw new \RuntimeException('Ride has not ended');
        }

        try {
            $this->summaryRepository->getByRideId($command->rideId);

            throw new \RuntimeException('Ride has already been summarised');
        } catch (SummaryNotFound) {
            $summary = new Summary(
                rideId: $command->rideId,
                duration: RideDuration::fromStartAndEnd($ride->getStartedAt(), $ride->getEndedAt()),
                route: $this->routeFetcher->fetch($ride),
            );

            $this->summaryRepository->store($summary);
        }
    }
}
