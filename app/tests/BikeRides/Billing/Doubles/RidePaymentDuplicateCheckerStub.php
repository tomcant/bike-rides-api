<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Doubles;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentDuplicateChecker;

final class RidePaymentDuplicateCheckerStub implements RidePaymentDuplicateChecker
{
    public function __construct(private bool $isDuplicate)
    {
    }

    public function setIsDuplicate(bool $isDuplicate): void
    {
        $this->isDuplicate = $isDuplicate;
    }

    public function isDuplicate(RideId $rideId): bool
    {
        return $this->isDuplicate;
    }
}
