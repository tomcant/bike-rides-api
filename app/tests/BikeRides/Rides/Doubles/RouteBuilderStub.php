<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Rides\Domain\Model\Ride\RouteBuilder;

final class RouteBuilderStub implements RouteBuilder
{
    public function __construct(private Route $route = new Route([]))
    {
    }

    public function build(Ride $ride): Route
    {
        return $this->route;
    }

    public function useRoute(Route $route): void
    {
        $this->route = $route;
    }
}
