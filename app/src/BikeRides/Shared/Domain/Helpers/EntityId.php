<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

use Symfony\Component\Uid\Uuid;

abstract readonly class EntityId
{
    private const NIL = '00000000-0000-0000-0000-000000000000';

    private function __construct(private string $id)
    {
        if (! Uuid::isValid($id)) {
            throw new \DomainException(\sprintf("'%s' is not a valid %s", $id, static::class));
        }
    }

    public function toString(): string
    {
        return $this->id;
    }

    public static function generate(): static
    {
        return new static(Uuid::v4()->toRfc4122());
    }

    public static function fromString(string $id): static
    {
        return new static($id);
    }

    public static function nil(): static
    {
        return new static(self::NIL);
    }

    public function equals(self $that): bool
    {
        if (static::class !== \get_class($that)) {
            throw new \DomainException('Unable to compare different types of ID');
        }

        return $this->id === $that->id;
    }

    public function isNil(): bool
    {
        return self::NIL === $this->id;
    }
}
