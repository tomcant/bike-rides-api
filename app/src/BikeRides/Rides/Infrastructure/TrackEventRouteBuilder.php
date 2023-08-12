<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Rides\Domain\Model\Ride\RouteBuilder;
use App\BikeRides\Rides\Domain\Model\Track\TrackRepository;

final readonly class TrackEventRouteBuilder implements RouteBuilder
{
    public function __construct(private TrackRepository $trackRepository)
    {
    }

    public function build(Ride $ride): Route
    {
        $trackEvents = $this->trackRepository->getBetweenForBikeId(
            $ride->getStartedAt(),
            $ride->getEndedAt(),
            $ride->getBikeId(),
        );

        return new Route(
            \array_combine(
                \array_map(
                    static fn ($trackEvent) => $trackEvent->trackedAt->getTimestamp(),
                    $trackEvents,
                ),
                \array_map(
                    static fn ($trackEvent) => $trackEvent->location,
                    $trackEvents,
                ),
            ),
        );
    }
}
