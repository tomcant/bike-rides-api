<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

use Symfony\Component\Uid\Uuid;

abstract readonly class EntityId
{
    private const string NIL = '00000000-0000-0000-0000-000000000000';

    final private function __construct(private string $id)
    {
        if (!Uuid::isValid($id)) {
            throw new \DomainException(\sprintf("'%s' is not a valid %s", $id, static::class));
        }
    }

    final public function toString(): string
    {
        return $this->id;
    }

    final public static function generate(): static
    {
        return new static(Uuid::v4()->toRfc4122());
    }

    final public static function fromString(string $id): static
    {
        return new static($id);
    }

    final public static function nil(): static
    {
        return new static(self::NIL);
    }

    final public function equals(self $that): bool
    {
        if (static::class !== $that::class) {
            throw new \DomainException('Unable to compare different types of ID');
        }

        return $this->id === $that->id;
    }

    final public function isNil(): bool
    {
        return self::NIL === $this->id;
    }
}
