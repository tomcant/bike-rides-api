<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\Foundation\Json;

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
