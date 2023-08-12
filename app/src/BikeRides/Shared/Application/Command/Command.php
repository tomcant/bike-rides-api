<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Application\Command;

interface Command
{
    public function serialize(): string;
}
