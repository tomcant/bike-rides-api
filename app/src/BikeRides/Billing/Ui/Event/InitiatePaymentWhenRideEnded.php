<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Ui\Event;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\RidePaymentAlreadyExists;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\BikeRides\Shared\Domain\Event\RideEnded;
use App\BikeRides\Shared\Domain\Helpers\DomainEventSubscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class InitiatePaymentWhenRideEnded implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus, private LoggerInterface $logger)
    {
    }

    public function __invoke(RideEnded $event): void
    {
        try {
            $this->bus->dispatch(new InitiateRidePaymentCommand(Uuid::v4()->toRfc4122(), $event->rideId));
        } catch (RidePaymentAlreadyExists) {
            $this->logger->notice('Duplicate ride payment', ['rideId' => $event->rideId]);
        }
    }
}
