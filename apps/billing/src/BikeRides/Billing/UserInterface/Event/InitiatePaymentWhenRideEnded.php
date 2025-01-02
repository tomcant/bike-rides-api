<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\UserInterface\Event;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\RidePaymentAlreadyInitiated;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\SharedKernel\Domain\Event\RideEnded;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class InitiatePaymentWhenRideEnded implements DomainEventSubscriber
{
    public function __construct(
        private CommandBus $bus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(RideEnded $event): void
    {
        try {
            $this->bus->dispatch(
                new InitiateRidePaymentCommand(
                    ridePaymentId: Uuid::v4()->toRfc4122(),
                    rideId: $event->rideId,
                ),
            );
        } catch (RidePaymentAlreadyInitiated) {
            $this->logger->notice('Duplicate ride payment', ['rideId' => $event->rideId]);
        }
    }
}
