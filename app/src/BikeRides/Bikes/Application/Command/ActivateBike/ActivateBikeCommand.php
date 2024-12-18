<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\ActivateBike;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Json;

final readonly class ActivateBikeCommand implements Command
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
