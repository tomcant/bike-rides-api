<?php

declare(strict_types=1);

namespace BikeRides\SharedKernel\Domain\Event;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\Location;

final readonly class BikeActivated extends DomainEvent
{
    public function __construct(
        public string $bikeId,
        public Location $location,
    ) {
        parent::__construct();
    }

    public function type(): string
    {
        return 'bike-activated';
    }

    public function version(): int
    {
        return 1;
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId,
            'location' => $this->location->toArray(),
        ]);
    }
}
