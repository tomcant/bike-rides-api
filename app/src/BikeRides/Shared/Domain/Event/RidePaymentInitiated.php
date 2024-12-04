<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;

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
        return \json_encode_array([
            'ridePaymentId' => $this->ridePaymentId,
            'rideId' => $this->rideId,
        ]);
    }
}
