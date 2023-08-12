<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Query;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;

final readonly class ListRidePaymentsByRideId
{
    public function __construct(private RidePaymentProjectionRepository $repository)
    {
    }

    public function query(string $rideId): array
    {
        return \array_map(
            static fn (RidePayment $ridePayment) => [
                'ride_payment_id' => $ridePayment->ridePaymentId,
                'ride_id' => $ridePayment->rideId,
                'total_price' => $ridePayment->totalPrice,
                'price_per_minute' => $ridePayment->pricePerMinute,
                'initiated_at' => $ridePayment->initiatedAt,
            ],
            $this->repository->listByRideId($rideId),
        );
    }
}
