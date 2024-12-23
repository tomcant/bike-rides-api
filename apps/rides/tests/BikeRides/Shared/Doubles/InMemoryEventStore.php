<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateEventFactory;
use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\Foundation\Domain\EntityId;
use BikeRides\Foundation\Domain\EventStore;

final class InMemoryEventStore implements EventStore
{
    private AggregateEvents $events;

    public function __construct(private readonly AggregateEventFactory $aggregateEventFactory)
    {
        $this->events = AggregateEvents::make();
    }

    public function store(AggregateEvents $events): void
    {
        $this->events = $events->reduce(
            fn (AggregateEvents $events, AggregateEvent $event) => $events->add($this->toSerializedAndBack($event)),
            $this->events,
        );
    }

    public function get(AggregateName $name, EntityId $id): AggregateEvents
    {
        return $this->events->reduce(
            fn (AggregateEvents $events, AggregateEvent $event) => $event->getAggregateName()->equals($name) && $event->getAggregateId()->equals($id)
                    ? $events->add($this->toSerializedAndBack($event))
                    : $events,
            AggregateEvents::make(),
        );
    }

    /**
     * This exercises the (de)serialisation logic which would otherwise only be
     * executed during persistence/integration layer tests.
     */
    private function toSerializedAndBack(AggregateEvent $event): AggregateEvent
    {
        return $this->aggregateEventFactory->fromSerialized($event->getEventName(), $event->serialize());
    }
}
