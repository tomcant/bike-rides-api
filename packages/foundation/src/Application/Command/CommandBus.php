<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Application\Command;

interface CommandBus
{
    /** @throws CommandNotRegistered */
    public function dispatch(Command $command): void;
}
