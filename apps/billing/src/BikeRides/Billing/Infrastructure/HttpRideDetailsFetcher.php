<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\RideDetailsFetcher;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpRideDetailsFetcher implements RideDetailsFetcher
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $rideDetailsApiUrlTemplate,
    ) {
    }

    public function fetch(RideId $rideId): RideDetails
    {
        $rideDetailsApiUrl = \str_replace('{rideId}', $rideId->toString(), $this->rideDetailsApiUrlTemplate);
        $response = $this->httpClient->request('GET', $rideDetailsApiUrl)->toArray();

        return new RideDetails(
            RideDuration::fromStartAndEnd(
                (new \DateTimeImmutable())->setTimestamp($response['started_at']),
                (new \DateTimeImmutable())->setTimestamp($response['ended_at']),
            ),
        );
    }
}
