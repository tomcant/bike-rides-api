<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Application\Command\SummariseRide\RouteFetcher;
use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\Route;

final class RouteFetcherStub implements RouteFetcher
{
    public function __construct(private Route $route = new Route([]))
    {
    }

    public function fetch(Ride $ride): Route
    {
        return $this->route;
    }

    public function useRoute(Route $route): void
    {
        $this->route = $route;
    }
}
