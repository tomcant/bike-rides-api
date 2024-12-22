<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Application\Command;

interface Command
{
    public function serialize(): string;
}
