<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\Foundation\Json;

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
