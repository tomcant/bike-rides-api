<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Infrastructure;

use App\BikeRides\Rides\Infrastructure\HttpBikeLocationFetcher;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class HttpBikeLocationFetcherTest extends TestCase
{
    private const array BIKE_API_RESPONSE = [
        '_links' => [
            'self' => [
                'href' => 'http://self',
                'method' => 'GET',
            ],
        ],
        'bike_id' => 1,
        'is_active' => true,
        'location' => [
            'latitude' => 1,
            'longitude' => 1,
        ],
    ];

    public function test_it_makes_an_http_request_to_fetch_a_bike_location(): void
    {
        $bikeId = BikeId::fromInt(1);
        $httpClient = new MockHttpClient($response = new JsonMockResponse(self::BIKE_API_RESPONSE));

        $fetcher = new HttpBikeLocationFetcher($httpClient, getBikeApiUrlTemplate: 'http://bikes-api/{bikeId}');
        $location = $fetcher->fetch($bikeId);

        self::assertSame('GET', $response->getRequestMethod());
        self::assertSame("http://bikes-api/{$bikeId->toInt()}", $response->getRequestUrl());
        self::assertEquals(Location::fromArray(self::BIKE_API_RESPONSE['location']), $location);
    }
}
