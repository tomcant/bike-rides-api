<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Application\Command\SummariseRide\RouteFetcher;
use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Summary\Route;
use BikeRides\SharedKernel\Domain\Model\Location;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpRouteFetcher implements RouteFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $trackingApiUrlTemplate,
    ) {
    }

    public function fetch(Ride $ride): Route
    {
        $trackingApiUrl = \str_replace(
            ['{bikeId}', '{from}', '{to}'],
            [
                (string) $ride->getBikeId()->toInt(),
                (string) $ride->getStartedAt()->getTimestamp(),
                (string) $ride->getEndedAt()->getTimestamp(),
            ],
            $this->trackingApiUrlTemplate,
        );

        $tracking = $this->httpClient->request('GET', $trackingApiUrl)->toArray();

        return new Route(
            \array_combine(
                \array_map(
                    static fn ($event) => $event['tracked_at'],
                    $tracking['_embedded']['tracking_event'],
                ),
                \array_map(
                    static fn ($event) => Location::fromArray($event['location']),
                    $tracking['_embedded']['tracking_event'],
                ),
            ),
        );
    }
}
