<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Shared;

final readonly class RiderId
{
    private function __construct(private string $riderId)
    {
    }

    public static function fromString(string $riderId): self
    {
        return new self($riderId);
    }

    public function toString(): string
    {
        return $this->riderId;
    }
}
