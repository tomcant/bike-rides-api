<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use BikeRides\SharedKernel\Domain\Model\RideDuration;
use Money\Money;

final readonly class RidePrice
{
    public function __construct(
        public Money $totalPrice,
        public Money $pricePerMinute,
        public RideDuration $rideDuration,
    ) {
    }

    /**
     * @return array{
     *   totalPrice: array{
     *     amount: string,
     *     currency: string,
     *   },
     *   pricePerMinute: array{
     *     amount: string,
     *     currency: string,
     *   },
     *   rideDuration: array{
     *     startedAt: string,
     *     endedAt: string,
     *     minutes: int,
     *   },
     * }
     */
    public function toArray(): array
    {
        return [
            'totalPrice' => $this->totalPrice->jsonSerialize(),
            'pricePerMinute' => $this->pricePerMinute->jsonSerialize(),
            'rideDuration' => $this->rideDuration->toArray(),
        ];
    }

    /**
     * @param array{
     *   totalPrice: array{
     *     amount: string,
     *     currency: string,
     *   },
     *   pricePerMinute: array{
     *     amount: string,
     *     currency: string,
     *   },
     *   rideDuration: array{
     *     startedAt: string,
     *     endedAt: string,
     *     minutes: int,
     *   },
     * } $ridePrice
     */
    public static function fromArray(array $ridePrice): self
    {
        return new self(
            \money_from_array($ridePrice['totalPrice']),
            \money_from_array($ridePrice['pricePerMinute']),
            RideDuration::fromArray($ridePrice['rideDuration']),
        );
    }
}
