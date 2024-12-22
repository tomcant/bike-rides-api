<?php

declare(strict_types=1);

namespace BikeRides\SharedKernel\Domain\Event;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;

final readonly class RidePaymentInitiated extends DomainEvent
{
    public function __construct(
        public string $ridePaymentId,
        public string $rideId,
    ) {
        parent::__construct();
    }

    public function serialize(): string
    {
        return Json::encode([
            'ridePaymentId' => $this->ridePaymentId,
            'rideId' => $this->rideId,
        ]);
    }
}
