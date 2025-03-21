<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Projection\RidePayment;

use BikeRides\Foundation\Money\Money;

final class RidePayment
{
    public function __construct(
        public readonly string $ridePaymentId,
        public readonly string $rideId,
        public readonly Money $totalPrice,
        public readonly Money $pricePerMinute,
        public readonly \DateTimeImmutable $initiatedAt,
        public ?\DateTimeImmutable $capturedAt = null,
        public ?string $externalRef = null,
    ) {
    }

    public static function initiate(
        string $ridePaymentId,
        string $rideId,
        Money $totalPrice,
        Money $pricePerMinute,
        \DateTimeImmutable $initiatedAt,
    ): self {
        return new self(
            $ridePaymentId,
            $rideId,
            $totalPrice,
            $pricePerMinute,
            $initiatedAt,
        );
    }

    public function capture(\DateTimeImmutable $capturedAt, string $externalRef): void
    {
        $this->capturedAt = $capturedAt;
        $this->externalRef = $externalRef;
    }
}
