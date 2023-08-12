<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\StoreRider;

use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use App\BikeRides\Shared\Application\Command\Command;

final readonly class StoreRiderCommand implements Command
{
    public RiderId $riderId;

    public function __construct(string $riderId)
    {
        $this->riderId = RiderId::fromString($riderId);
    }

    public function serialize(): string
    {
        return \json_encode_array([
            'riderId' => $this->riderId->toString(),
        ]);
    }
}
