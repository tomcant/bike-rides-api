<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

interface AggregateEventsBus
{
    public function publish(AggregateEvents $events): void;
}
