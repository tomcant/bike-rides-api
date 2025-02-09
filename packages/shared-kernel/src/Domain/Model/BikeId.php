<?php

declare(strict_types=1);

namespace BikeRides\SharedKernel\Domain\Model;

final readonly class BikeId
{
    private function __construct(public int $id)
    {
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public function toInt(): int
    {
        return $this->id;
    }
}
