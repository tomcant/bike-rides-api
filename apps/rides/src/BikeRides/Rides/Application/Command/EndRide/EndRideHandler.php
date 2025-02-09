<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\EndRide;

use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\Foundation\Domain\TransactionBoundary;
use BikeRides\SharedKernel\Domain\Event\RideEnded;

final readonly class EndRideHandler implements CommandHandler
{
    public function __construct(
        private RideRepository $rideRepository,
        private TransactionBoundary $transaction,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(EndRideCommand $command): void
    {
        $ride = $this->rideRepository->getById($command->rideId);

        $ride->end();

        $this->transaction->begin();

        try {
            $this->rideRepository->store($ride);

            $this->eventBus->publish(
                new RideEnded(
                    $command->rideId->toString(),
                    $ride->getBikeId()->toInt(),
                ),
            );
        } catch (\Throwable $exception) {
            $this->transaction->abort();

            throw $exception;
        }

        $this->transaction->end();
    }
}
