<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use BikeRides\Foundation\Money\Money;
use BikeRides\SharedKernel\Domain\Model\RideDuration;

final readonly class RidePriceCalculator
{
    private const int PRICE_PER_MINUTE_IN_PENCE = 25;

    public function calculatePrice(RideDuration $rideDuration): RidePrice
    {
        $pricePerMinute = Money::GBP(self::PRICE_PER_MINUTE_IN_PENCE);
        $totalPrice = $pricePerMinute->multiply($rideDuration->minutes);

        return new RidePrice($totalPrice, $pricePerMinute, $rideDuration);
    }
}
