<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use App\BikeRides\Shared\Domain\Model\RideDuration;
use Money\Money;

final readonly class RidePrice
{
    public function __construct(
        public Money $totalPrice,
        public Money $pricePerMinute,
        public RideDuration $rideDuration,
    ) {
    }

    public function toArray(): array
    {
        return [
            'totalPrice' => $this->totalPrice->jsonSerialize(),
            'pricePerMinute' => $this->pricePerMinute->jsonSerialize(),
            'rideDuration' => $this->rideDuration->toArray(),
        ];
    }

    public static function fromArray(array $ridePrice): self
    {
        return new self(
            \money_from_array($ridePrice['totalPrice']),
            \money_from_array($ridePrice['pricePerMinute']),
            RideDuration::fromArray($ridePrice['rideDuration']),
        );
    }
}
