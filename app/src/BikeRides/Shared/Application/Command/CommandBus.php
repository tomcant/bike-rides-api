<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Application\Command;

interface CommandBus
{
    /** @throws CommandNotRegistered */
    public function dispatch(Command $command): void;
}
