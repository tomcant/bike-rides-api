<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Event;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\Foundation\Json;
use App\Foundation\Location;

final readonly class BikeLocated extends DomainEvent
{
    public function __construct(
        public string $bikeId,
        public Location $location,
        public \DateTimeImmutable $locatedAt,
    ) {
        parent::__construct();
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId,
            'location' => $this->location->toArray(),
            'locatedAt' => \datetime_timestamp($this->locatedAt),
        ]);
    }
}
