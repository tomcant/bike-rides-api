<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\AddPriceToSummary\AddPriceToSummaryCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\Foundation\Money\Money;
use BikeRides\SharedKernel\Domain\Event\RidePaymentInitiated;

final readonly class AddPriceToSummaryWhenRidePaymentInitiated implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(RidePaymentInitiated $event): void
    {
        $this->bus->dispatch(
            new AddPriceToSummaryCommand(
                $event->rideId,
                Money::fromArray($event->ridePrice['totalPrice']),
            ),
        );
    }
}
