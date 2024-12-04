<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

interface EventStore
{
    public function store(AggregateEvents $events): void;

    public function get(AggregateName $name, EntityId $id): AggregateEvents;
}
