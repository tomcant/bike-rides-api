<?php

declare(strict_types=1);

namespace App\Foundation;

final class Location
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!\is_numeric($data['latitude'] ?? null) || !\is_numeric($data['longitude'] ?? null)) {
            throw new \InvalidArgumentException('Numeric values required for "latitude" and "longitude"');
        }

        return new self(
            (float) $data['latitude'],
            (float) $data['longitude'],
        );
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
