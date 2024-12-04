<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\UserInterface\Event;

use App\BikeRides\Billing\Application\Command\CaptureRidePayment\CaptureRidePaymentCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\BikeRides\Shared\Domain\Event\RidePaymentInitiated;
use App\BikeRides\Shared\Domain\Helpers\DomainEventSubscriber;

final readonly class AttemptCaptureWhenPaymentInitiated implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(RidePaymentInitiated $event): void
    {
        $this->bus->dispatch(new CaptureRidePaymentCommand($event->ridePaymentId));
    }
}
