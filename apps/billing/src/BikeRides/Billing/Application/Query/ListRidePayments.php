<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Query;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;
use BikeRides\Foundation\Money\Money;

final readonly class ListRidePayments
{
    public function __construct(private RidePaymentProjectionRepository $repository)
    {
    }

    /**
     * @return list<array{
     *    ride_payment_id: string,
     *    ride_id: string,
     *    total_price: Money,
     *    price_per_minute: Money,
     *    initiated_at: \DateTimeImmutable,
     *    captured_at: ?\DateTimeImmutable,
     *    external_payment_ref: ?string,
     *  }>
     */
    public function query(): array
    {
        return \array_map(
            static fn (RidePayment $ridePayment) => [
                'ride_payment_id' => $ridePayment->ridePaymentId,
                'ride_id' => $ridePayment->rideId,
                'total_price' => $ridePayment->totalPrice,
                'price_per_minute' => $ridePayment->pricePerMinute,
                'initiated_at' => $ridePayment->initiatedAt,
                'captured_at' => $ridePayment->capturedAt,
                'external_payment_ref' => $ridePayment->externalRef,
            ],
            $this->repository->list(),
        );
    }
}
