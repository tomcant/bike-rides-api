<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\BikeLocationFetcher;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpBikeLocationFetcher implements BikeLocationFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $bikeApiUrlTemplate,
    ) {
    }

    public function fetch(BikeId $bikeId): Location
    {
        $bikeApiUrl = \str_replace('{bikeId}', $bikeId->toString(), $this->bikeApiUrlTemplate);
        $bike = $this->httpClient->request('GET', $bikeApiUrl)->toArray();

        return Location::fromArray($bike['location']);
    }
}
