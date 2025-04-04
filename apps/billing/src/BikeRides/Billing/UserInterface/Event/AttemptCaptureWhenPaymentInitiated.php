<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\UserInterface\Event;

use App\BikeRides\Billing\Application\Command\CaptureRidePayment\CaptureRidePaymentCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\SharedKernel\Domain\Event\RidePaymentInitiated;

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
