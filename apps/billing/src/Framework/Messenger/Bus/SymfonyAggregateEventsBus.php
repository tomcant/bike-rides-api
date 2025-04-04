<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Bus;

use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\Foundation\Domain\AggregateEventsBus;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyAggregateEventsBus implements AggregateEventsBus
{
    public function __construct(
        #[Autowire(service: 'aggregate_events.bus')]
        private MessageBusInterface $bus,
    ) {
    }

    public function publish(AggregateEvents $events): void
    {
        $this->bus->dispatch($events);
    }
}
