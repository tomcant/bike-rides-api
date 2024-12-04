<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;

final class DomainEventBusSpy implements DomainEventBus
{
    public ?DomainEvent $lastEvent = null;

    public function publish(DomainEvent $event): void
    {
        $this->lastEvent = $event;
    }
}
