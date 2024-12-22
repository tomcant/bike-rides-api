<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Domain\DomainEventBus;

final readonly class DomainEventBusDummy implements DomainEventBus
{
    public function publish(DomainEvent $event): void
    {
    }
}
