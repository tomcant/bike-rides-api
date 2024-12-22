<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

interface EventStore
{
    public function store(AggregateEvents $events): void;

    public function get(AggregateName $name, EntityId $id): AggregateEvents;
}
