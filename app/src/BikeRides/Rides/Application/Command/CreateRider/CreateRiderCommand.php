<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\CreateRider;

use App\BikeRides\Shared\Domain\Model\RiderId;
use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;

final readonly class CreateRiderCommand implements Command
{
    public RiderId $riderId;

    public function __construct(string $riderId)
    {
        $this->riderId = RiderId::fromString($riderId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'riderId' => $this->riderId->toString(),
        ]);
    }
}
