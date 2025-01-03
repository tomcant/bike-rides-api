<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Infrastructure\HttpRideDetailsFetcher;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class HttpRideDetailsFetcherTest extends TestCase
{
    private const array RIDE_DETAILS_API_RESPONSE = [
        '_links' => [
            'self' => ['href' => 'http://self', 'method' => 'GET'],
            'end' => ['href' => 'http://end', 'method' => 'GET'],
            'summary' => ['href' => 'http://summary', 'method' => 'GET'],
        ],
        'ride_id' => 'ride_id',
        'rider_id' => 'rider_id',
        'bike_id' => 'bike_id',
        'started_at' => self::RIDE_STARTED_TIMESTAMP,
        'ended_at' => self::RIDE_ENDED_TIMESTAMP,
    ];
    private const int RIDE_STARTED_TIMESTAMP = 1;
    private const int RIDE_ENDED_TIMESTAMP = 2;

    public function test_it_makes_an_http_request_to_fetch_ride_details(): void
    {
        $rideId = RideId::generate();
        $httpClient = new MockHttpClient($response = new JsonMockResponse(self::RIDE_DETAILS_API_RESPONSE));

        $fetcher = new HttpRideDetailsFetcher($httpClient, rideDetailsApiUrlTemplate: 'http://rides-api/ride/{rideId}');
        $rideDetails = $fetcher->fetch($rideId);

        self::assertSame('GET', $response->getRequestMethod());
        self::assertSame("http://rides-api/ride/{$rideId->toString()}", $response->getRequestUrl());
        self::assertEquals(
            new RideDetails(
                RideDuration::fromStartAndEnd(
                    (new \DateTimeImmutable())->setTimestamp(self::RIDE_STARTED_TIMESTAMP),
                    (new \DateTimeImmutable())->setTimestamp(self::RIDE_ENDED_TIMESTAMP),
                ),
            ),
            $rideDetails,
        );
    }
}
