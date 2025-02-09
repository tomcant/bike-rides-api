<?php

declare(strict_types=1);

namespace BikeRides\SharedKernel\Domain\Event;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;

final readonly class BikeDeactivated extends DomainEvent
{
    public function __construct(public int $bikeId)
    {
        parent::__construct();
    }

    public function type(): string
    {
        return 'bike-deactivated';
    }

    public function version(): int
    {
        return 1;
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId,
        ]);
    }
}
