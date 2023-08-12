<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\SummariseRide;

use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use App\BikeRides\Rides\Domain\Model\Ride\RouteBuilder;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class SummariseRideHandler implements CommandHandler
{
    public function __construct(
        private RideRepository $rideRepository,
        private RouteBuilder $routeBuilder,
    ) {
    }

    public function __invoke(SummariseRideCommand $command): void
    {
        $ride = $this->rideRepository->getById($command->rideId);

        $ride->summarise($this->routeBuilder);

        $this->rideRepository->store($ride);
    }
}
