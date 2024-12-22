<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

final readonly class AggregateName
{
    private function __construct(private string $name)
    {
        if ('' === $name) {
            throw new \DomainException('Aggregates must have a name');
        }
    }

    public function toString(): string
    {
        return $this->name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function equals(self $that): bool
    {
        return $this->name === $that->name;
    }
}
