<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\SummariseRide;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\Route;

interface RouteFetcher
{
    public function fetch(Ride $ride): Route;
}
