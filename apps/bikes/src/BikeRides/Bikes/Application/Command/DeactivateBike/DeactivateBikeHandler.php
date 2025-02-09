<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\DeactivateBike;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\Foundation\Domain\TransactionBoundary;
use BikeRides\SharedKernel\Domain\Event\BikeDeactivated;

final readonly class DeactivateBikeHandler implements CommandHandler
{
    public function __construct(
        private BikeRepository $bikeRepository,
        private TransactionBoundary $transaction,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(DeactivateBikeCommand $command): void
    {
        $bike = $this->bikeRepository->getById($command->bikeId);

        $bike->deactivate();

        $this->transaction->begin();

        try {
            $this->bikeRepository->store($bike);
            $this->eventBus->publish(new BikeDeactivated($command->bikeId->toInt()));
        } catch (\Throwable $exception) {
            $this->transaction->abort();

            throw $exception;
        }

        $this->transaction->end();
    }
}
