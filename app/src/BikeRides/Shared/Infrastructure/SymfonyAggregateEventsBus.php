<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Infrastructure;

use App\BikeRides\Shared\Domain\Helpers\AggregateEvents;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventsBus;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyAggregateEventsBus implements AggregateEventsBus
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function publish(AggregateEvents $events): void
    {
        $this->bus->dispatch($events);
    }
}
