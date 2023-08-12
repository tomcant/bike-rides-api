<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\Foundation\Location;

final readonly class BikeTracked extends DomainEvent
{
    public function __construct(
        public string $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
        parent::__construct();
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'bikeId' => $this->bikeId,
            'location' => $this->location->toArray(),
            'trackedAt' => \datetime_timestamp($this->trackedAt),
        ]);
    }
}
