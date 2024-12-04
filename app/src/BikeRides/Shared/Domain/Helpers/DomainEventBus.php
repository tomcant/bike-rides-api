<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

interface DomainEventBus
{
    public function publish(DomainEvent $event): void;
}
