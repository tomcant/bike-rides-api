<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Rides\Domain\Model\Ride\RouteFetcher;
use App\Foundation\Location;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpRouteFetcher implements RouteFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $bikeLocationsApiUrlTemplate,
    ) {
    }

    public function fetch(Ride $ride): Route
    {
        $bikeLocationsApiUrl = \str_replace(
            [
                '{bikeId}',
                '{from}',
                '{to}',
            ],
            [
                $ride->getBikeId()->toString(),
                $ride->getStartedAt()->getTimestamp(),
                $ride->getEndedAt()->getTimestamp(),
            ],
            $this->bikeLocationsApiUrlTemplate,
        );

        $bikeLocations = $this->httpClient->request('GET', $bikeLocationsApiUrl)->toArray();

        return new Route(
            \array_combine(
                \array_map(
                    static fn ($bikeLocation) => $bikeLocation['locatedAt'],
                    $bikeLocations['_embedded']['bike_location'],
                ),
                \array_map(
                    static fn ($bikeLocation) => Location::fromArray($bikeLocation['location']),
                    $bikeLocations['_embedded']['bike_location'],
                ),
            ),
        );
    }
}
