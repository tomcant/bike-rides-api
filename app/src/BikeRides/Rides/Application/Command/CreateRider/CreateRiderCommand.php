<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\CreateRider;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\RiderId;

final readonly class CreateRiderCommand implements Command
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
