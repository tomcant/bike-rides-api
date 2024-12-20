<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use App\BikeRides\Shared\Domain\Model\RideDuration;

final readonly class RideDetails
{
    public function __construct(public RideDuration $duration)
    {
    }

    /**
     * @return array{
     *   duration: array{
     *     startedAt: string,
     *     endedAt: string,
     *     minutes: int,
     *   },
     * }
     */
    public function toArray(): array
    {
        return ['duration' => $this->duration->toArray()];
    }

    /**
     * @param array{
     *   duration: array{
     *     startedAt: string,
     *     endedAt: string,
     *     minutes: int,
     *   },
     * } $rideDetails
     */
    public static function fromArray(array $rideDetails): self
    {
        return new self(RideDuration::fromArray($rideDetails['duration']));
    }
}
