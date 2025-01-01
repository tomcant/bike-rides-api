<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\SummariseRide;

use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;

final readonly class SummariseRideHandler implements CommandHandler
{
    public function __construct(
        private RideRepository $rideRepository,
        private RouteFetcher $routeFetcher,
    ) {
    }

    public function __invoke(SummariseRideCommand $command): void
    {
        $ride = $this->rideRepository->getById($command->rideId);

        $route = $this->routeFetcher->fetch($ride);

        $ride->summarise($route);

        $this->rideRepository->store($ride);
    }
}
