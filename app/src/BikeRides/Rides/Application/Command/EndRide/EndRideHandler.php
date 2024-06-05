<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\EndRide;

use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;
use App\BikeRides\Shared\Domain\Event\RideEnded;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;

final readonly class EndRideHandler implements CommandHandler
{
    public function __construct(
        private RideRepository $rideRepository,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(EndRideCommand $command): void
    {
        $ride = $this->rideRepository->getById($command->rideId);

        $ride->end();

        $this->rideRepository->store($ride);

        $this->eventBus->publish(
            new RideEnded(
                $command->rideId->toString(),
                $ride->getBikeId()->toString(),
            ),
        );
    }
}
