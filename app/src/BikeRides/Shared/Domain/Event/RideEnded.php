<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;

final readonly class RideEnded extends DomainEvent
{
    public function __construct(
        public string $rideId,
        public string $bikeId,
    ) {
        parent::__construct();
    }

    public function serialize(): string
    {
        return Json::encode([
            'rideId' => $this->rideId,
            'bikeId' => $this->bikeId,
        ]);
    }
}
