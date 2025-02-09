<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\DeactivateBike;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final readonly class DeactivateBikeCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(int $bikeId)
    {
        $this->bikeId = BikeId::fromInt($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId->toInt(),
        ]);
    }
}
