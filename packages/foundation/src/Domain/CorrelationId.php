<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

use Symfony\Component\Uid\Uuid;

final readonly class CorrelationId
{
    private function __construct(private string $id)
    {
        if (empty($id)) {
            throw new \DomainException('Invalid correlation ID, cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function equals(self $that): bool
    {
        return $this->id === $that->id;
    }
}
