<?php

declare(strict_types=1);

namespace BikeRides\SharedKernel\Domain\Model;

final readonly class Location
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }

    /** @param array<mixed, mixed> $location */
    public static function fromArray(array $location): self
    {
        if (!\is_numeric($location['latitude'] ?? null) || !\is_numeric($location['longitude'] ?? null)) {
            throw new \InvalidArgumentException('Numeric values required for "latitude" and "longitude"');
        }

        return new self(
            (float) $location['latitude'],
            (float) $location['longitude'],
        );
    }

    /** @return array{latitude: float, longitude: float} */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
