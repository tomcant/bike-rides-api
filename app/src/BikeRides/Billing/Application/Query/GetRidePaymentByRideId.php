<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Query;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentNotFound;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;

final readonly class GetRidePaymentByRideId
{
    public function __construct(private RidePaymentProjectionRepository $repository)
    {
    }

    public function query(string $rideId): ?array
    {
        try {
            $ridePayment = $this->repository->getByRideId($rideId);
        } catch (RidePaymentNotFound) {
            return null;
        }

        return [
            'ride_payment_id' => $ridePayment->ridePaymentId,
            'ride_id' => $ridePayment->rideId,
            'total_price' => $ridePayment->totalPrice,
            'price_per_minute' => $ridePayment->pricePerMinute,
            'initiated_at' => $ridePayment->initiatedAt,
            'captured_at' => $ridePayment->capturedAt,
            'external_payment_ref' => $ridePayment->externalRef,
        ];
    }
}
