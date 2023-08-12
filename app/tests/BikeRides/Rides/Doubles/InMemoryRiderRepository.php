<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Rider\RiderNotFound;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;

final class InMemoryRiderRepository implements RiderRepository
{
    /** @var array<Rider> */
    private array $riders;

    public function store(Rider $rider): void
    {
        $this->riders[$rider->riderId->toString()] = $rider;
    }

    public function getById(RiderId $riderId): Rider
    {
        return $this->riders[$riderId->toString()] ?? throw new RiderNotFound($riderId);
    }
}
