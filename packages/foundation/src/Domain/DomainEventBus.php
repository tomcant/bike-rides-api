<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

interface DomainEventBus
{
    public function publish(DomainEvent $event): void;
}
