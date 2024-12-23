<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

final readonly class ExternalPaymentRef
{
    private function __construct(public string $ref)
    {
    }

    public static function fromString(string $ref): self
    {
        return new self($ref);
    }

    public function toString(): string
    {
        return $this->ref;
    }
}
