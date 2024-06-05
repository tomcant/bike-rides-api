<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

interface RouteFetcher
{
    public function fetch(Ride $ride): Route;
}
