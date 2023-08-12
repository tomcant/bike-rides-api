<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;

final readonly class RideEnded extends DomainEvent
{
    public function __construct(public string $rideId)
    {
        parent::__construct();
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'rideId' => $this->rideId,
        ]);
    }
}
