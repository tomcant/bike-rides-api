<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentGateway;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use Psr\Log\LoggerInterface;

final readonly class LoggingRidePaymentGateway implements RidePaymentGateway
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function capture(RidePaymentId $ridePaymentId): ExternalPaymentRef
    {
        $externalPaymentRef = 'external_payment_ref';

        $this->logger->info('Ride payment captured', [
            'ridePaymentId' => $ridePaymentId->toString(),
            'externalPaymentRef' => $externalPaymentRef,
        ]);

        return ExternalPaymentRef::fromString($externalPaymentRef);
    }
}
