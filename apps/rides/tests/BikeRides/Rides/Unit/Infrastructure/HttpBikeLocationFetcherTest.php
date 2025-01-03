<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Infrastructure\HttpBikeLocationFetcher;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class HttpBikeLocationFetcherTest extends TestCase
{
    private const array BIKE_TRACKING_API_RESPONSE = [
        '_links' => [
            'self' => ['href' => 'http://self', 'method' => 'GET'],
            'bike' => ['href' => 'http://bike', 'method' => 'GET'],
        ],
        '_embedded' => [
            'tracking_event' => [
                [
                    'location' => ['latitude' => 0, 'longitude' => 0],
                    'tracked_at' => 1,
                ],
                [
                    'location' => self::LAST_TRACKING_EVENT_LOCATION,
                    'tracked_at' => 2,
                ],
            ],
        ],
        'total' => 2,
    ];
    private const array LAST_TRACKING_EVENT_LOCATION = ['latitude' => 1, 'longitude' => 1];

    public function test_it_makes_an_http_request_to_fetch_a_bike_location(): void
    {
        $bikeId = BikeId::generate();
        $bike = new Bike($bikeId, new Location(0, 0));
        $httpClient = new MockHttpClient($response = new JsonMockResponse(self::BIKE_TRACKING_API_RESPONSE));

        $fetcher = new HttpBikeLocationFetcher($httpClient, trackingApiUrlTemplate: 'http://bikes-api/{bikeId}/{from}/{to}');
        $location = $fetcher->fetch($bikeId);

        self::assertSame('GET', $response->getRequestMethod());
        self::assertStringStartsWith("http://bikes-api/{$bikeId->toString()}/", $response->getRequestUrl());
        self::assertEquals(Location::fromArray(self::LAST_TRACKING_EVENT_LOCATION), $location);
    }
}
