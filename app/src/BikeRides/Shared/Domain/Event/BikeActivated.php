<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\Foundation\Location;
use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;

final readonly class BikeActivated extends DomainEvent
{
    public function __construct(
        public string $bikeId,
        public Location $location,
    ) {
        parent::__construct();
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId,
            'location' => $this->location->toArray(),
        ]);
    }
}
