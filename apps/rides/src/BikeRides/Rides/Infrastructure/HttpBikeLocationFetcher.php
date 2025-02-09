<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\BikeLocationFetcher;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpBikeLocationFetcher implements BikeLocationFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $trackingApiUrlTemplate,
    ) {
    }

    public function fetch(BikeId $bikeId): Location
    {
        $trackingApiUrl = \str_replace(
            ['{bikeId}', '{from}', '{to}'],
            [
                (string) $bikeId->toInt(),
                (string) Clock::now()->modify('-1 minute')->getTimestamp(),
                (string) Clock::now()->getTimestamp(),
            ],
            $this->trackingApiUrlTemplate,
        );

        $tracking = $this->httpClient->request('GET', $trackingApiUrl)->toArray();

        $lastTrackingEvent = \end($tracking['_embedded']['tracking_event']);

        return Location::fromArray($lastTrackingEvent['location']);
    }
}
