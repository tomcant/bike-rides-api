<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Domain\DomainEventBus;

final class DomainEventBusSpy implements DomainEventBus
{
    public ?DomainEvent $lastEvent = null;

    public function publish(DomainEvent $event): void
    {
        $this->lastEvent = $event;
    }
}
