<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\RefreshBikeLocation;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final readonly class RefreshBikeLocationCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(string $bikeId)
    {
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId->toString(),
        ]);
    }
}
