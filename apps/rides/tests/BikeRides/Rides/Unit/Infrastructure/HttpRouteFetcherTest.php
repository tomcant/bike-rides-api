<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Rides\Infrastructure\HttpRouteFetcher;
use App\Tests\BikeRides\Rides\Doubles\BikeAvailabilityCheckerStub;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class HttpRouteFetcherTest extends TestCase
{
    private const array BIKE_TRACKING_API_RESPONSE = [
        '_links' => [
            'self' => ['href' => 'http://self', 'method' => 'GET'],
            'bike' => ['href' => 'http://bike', 'method' => 'GET'],
        ],
        '_embedded' => [
            'tracking_event' => [
                [
                    'location' => self::TRACKING_EVENT_1_LOCATION,
                    'tracked_at' => self::TRACKING_EVENT_1_TIMESTAMP,
                ],
                [
                    'location' => self::TRACKING_EVENT_2_LOCATION,
                    'tracked_at' => self::TRACKING_EVENT_2_TIMESTAMP,
                ],
            ],
        ],
        'total' => 2,
    ];
    private const int TRACKING_EVENT_1_TIMESTAMP = 1;
    private const int TRACKING_EVENT_2_TIMESTAMP = 2;
    private const array TRACKING_EVENT_1_LOCATION = ['latitude' => 0, 'longitude' => 0];
    private const array TRACKING_EVENT_2_LOCATION = ['latitude' => 1, 'longitude' => 1];

    public function test_it_makes_an_http_request_to_fetch_a_route(): void
    {
        $ride = Ride::start(
            RideId::generate(),
            RiderId::fromString('rider_id'),
            BikeId::generate(),
            BikeAvailabilityCheckerStub::available(),
        );
        $ride->end();
        $httpClient = new MockHttpClient($response = new JsonMockResponse(self::BIKE_TRACKING_API_RESPONSE));

        $fetcher = new HttpRouteFetcher($httpClient, trackingApiUrlTemplate: 'http://bikes-api/{bikeId}/{from}/{to}');
        $route = $fetcher->fetch($ride);

        self::assertSame('GET', $response->getRequestMethod());
        self::assertSame(
            \sprintf(
                'http://bikes-api/%s/%s/%s',
                $ride->getBikeId()->toString(),
                $ride->getStartedAt()->getTimestamp(),
                $ride->getEndedAt()->getTimestamp(),
            ),
            $response->getRequestUrl(),
        );
        self::assertEquals(
            new Route([
                self::TRACKING_EVENT_1_TIMESTAMP => Location::fromArray(self::TRACKING_EVENT_1_LOCATION),
                self::TRACKING_EVENT_2_TIMESTAMP => Location::fromArray(self::TRACKING_EVENT_2_LOCATION),
            ]),
            $route,
        );
    }
}
