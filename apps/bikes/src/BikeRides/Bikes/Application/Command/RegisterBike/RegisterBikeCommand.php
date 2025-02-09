<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\RegisterBike;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\Foundation\Json;

final readonly class RegisterBikeCommand implements Command
{
    public CorrelationId $correlationId;

    public function __construct(string $correlationId)
    {
        $this->correlationId = CorrelationId::fromString($correlationId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'correlationId' => $this->correlationId->toString(),
        ]);
    }
}
