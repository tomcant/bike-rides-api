<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\StartRide;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;

final readonly class StartRideHandler implements CommandHandler
{
    public function __construct(
        private RideRepository $rideRepository,
        private BikeAvailabilityChecker $bikeAvailabilityChecker,
    ) {
    }

    public function __invoke(StartRideCommand $command): void
    {
        if (!$this->bikeAvailabilityChecker->isAvailable($command->bikeId)) {
            throw new \RuntimeException(\sprintf('Bike "%s" is not available', $command->bikeId->toString()));
        }

        $ride = Ride::start($command->rideId, $command->riderId, $command->bikeId);

        $this->rideRepository->store($ride);
    }
}
