<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\BikeLocationFetcher;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpBikeLocationFetcher implements BikeLocationFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $getBikeApiUrlTemplate,
    ) {
    }

    public function fetch(BikeId $bikeId): Location
    {
        $getBikeApiUrl = \str_replace('{bikeId}', (string) $bikeId->toInt(), $this->getBikeApiUrlTemplate);

        $bike = $this->httpClient->request('GET', $getBikeApiUrl)->toArray();

        return Location::fromArray($bike['location']);
    }
}
