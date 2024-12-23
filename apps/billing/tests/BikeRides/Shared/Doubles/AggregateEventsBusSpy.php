<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\Foundation\Domain\AggregateEventsBus;

final class AggregateEventsBusSpy implements AggregateEventsBus
{
    private ?AggregateEvents $events = null;

    public function getLastEvents(): ?AggregateEvents
    {
        return $this->events;
    }

    public function publish(AggregateEvents $events): void
    {
        $this->events = $events;
    }
}
