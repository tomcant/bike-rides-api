<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\AddPriceToSummary;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Money\Money;

final readonly class AddPriceToSummaryCommand implements Command
{
    public RideId $rideId;

    public function __construct(
        string $rideId,
        public Money $price,
    ) {
        $this->rideId = RideId::fromString($rideId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'rideId' => $this->rideId->toString(),
            'price' => $this->price->jsonSerialize(),
        ]);
    }
}
