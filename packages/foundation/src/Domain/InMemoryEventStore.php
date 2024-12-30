<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

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
            fn (AggregateEvents $events, AggregateEvent $event) => $events->add($this->exerciseSerialization($event)),
            $this->events,
        );
    }

    public function get(AggregateName $name, EntityId $id): AggregateEvents
    {
        return $this->events->reduce(
            fn (AggregateEvents $events, AggregateEvent $event) => $event->getAggregateName()->equals($name) && $event->getAggregateId()->equals($id)
                    ? $events->add($this->exerciseSerialization($event))
                    : $events,
            AggregateEvents::make(),
        );
    }

    /*
     * This exercises (de)serialization of events, which probably wouldn't
     * otherwise happen in contexts where an in-memory repository is required.
     */
    private function exerciseSerialization(AggregateEvent $event): AggregateEvent
    {
        return $this->aggregateEventFactory->fromSerialized($event->getEventName(), $event->serialize());
    }
}
