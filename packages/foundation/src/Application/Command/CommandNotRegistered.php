<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Application\Command;

final class CommandNotRegistered extends \DomainException
{
    public function __construct(Command $command)
    {
        parent::__construct(\sprintf("The command '%s' is not registered with a handler", $command::class));
    }
}
