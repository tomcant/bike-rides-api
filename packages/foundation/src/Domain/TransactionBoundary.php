<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

interface TransactionBoundary
{
    public function begin(): void;

    public function end(): void;

    public function abort(): void;
}
