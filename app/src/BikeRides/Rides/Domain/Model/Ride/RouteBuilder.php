<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

interface RouteBuilder
{
    public function build(Ride $ride): Route;
}
