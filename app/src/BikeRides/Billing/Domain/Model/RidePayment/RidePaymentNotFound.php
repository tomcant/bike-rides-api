<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

final class RidePaymentNotFound extends \DomainException
{
    public function __construct(RidePaymentId $id)
    {
        parent::__construct(\sprintf("Unable to find ride payment with ID '%s'", $id->toString()));
    }
}
