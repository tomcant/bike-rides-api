<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\UserInterface;

use App\Foundation\Clock\Clock;
use App\Foundation\Location;
use Symfony\Component\Uid\Uuid;

final class BikeLocationsApiTest extends BikesUiTestCase
{
    public function test_bike_location_tracking(): void
    {
        $bike = $this->registerBike();
        $fromTimestamp = Clock::now();

        $this->postJson(
            '/bikes/bike/locate',
            [
                'bike_id' => $bike['bike_id'],
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
            ],
        );

        $bikeLocations = $this->listBikeLocations(
            bikeId: $bike['bike_id'],
            from: $fromTimestamp,
            to: Clock::now(),
        );

        self::assertCount(1, $bikeLocations['_embedded']['bike_location']);
        self::assertEquals($location, $bikeLocations['_embedded']['bike_location'][0]['location']);
    }

    /** @dataProvider invalidLocationParamProvider */
    public function test_bike_location_tracking_returns_400_response_for_invalid_location(array $location): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            '/bikes/bike/locate',
            ['bike_id' => $bike['bike_id'], ...$location],
            assertResponseIsSuccessful: false,
        );

        self::assertResponseStatusCodeSame(400);
    }

    public static function invalidLocationParamProvider(): iterable
    {
        yield from [
            'missing_location' => [[]],
            'missing_longitude' => [['location' => ['latitude' => 0]]],
            'missing_latitude' => [['location' => ['longitude' => 0]]],
        ];
    }

    public function test_current_bike_location_is_updated_when_bike_location_is_tracked(): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            '/bikes/bike/locate',
            [
                'bike_id' => $bike['bike_id'],
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
            ],
        );

        self::assertEquals($location, $this->retrieveBike($bike['bike_id'])['location']);
    }

    public function test_list_bike_locations(): void
    {
        $bike = $this->registerBike();
        $fromTimestamp = Clock::now()->getTimestamp();
        $this->locateBike($bike['bike_id'], $locationOne = new Location(0, 0));
        $this->locateBike($bike['bike_id'], $locationTwo = new Location(1, 1));
        $toTimestamp = Clock::now()->getTimestamp();

        $list = $this->getJson("/bikes/bike/{$bike['bike_id']}/locations?from={$fromTimestamp}&to={$toTimestamp}");

        self::assertCount(2, $list['_embedded']['bike_location']);
        self::assertSame(2, $list['total']);

        foreach ($list['_embedded']['bike_location'] as $embeddedBikeLocation) {
            self::assertArrayHasKeys($embeddedBikeLocation, ['location', 'locatedAt']);
            self::assertIsNumeric($embeddedBikeLocation['locatedAt']);
        }

        $listedLocations = \array_map(
            static fn ($embeddedBikeLocation) => $embeddedBikeLocation['location'],
            $list['_embedded']['bike_location'],
        );
        self::assertContainsEquals($locationOne->toArray(), $listedLocations);
        self::assertContainsEquals($locationTwo->toArray(), $listedLocations);
    }

    /** @dataProvider invalidTimeRangeParamProvider */
    public function test_list_bike_locations_returns_400_response_for_invalid_time_range(array $timeRange): void
    {
        $bike = $this->registerBike();
        $queryString = \http_build_query($timeRange);

        $this->client->request('GET', "/bikes/bike/{$bike['bike_id']}/locations?{$queryString}");

        self::assertResponseStatusCodeSame(400);
    }

    public static function invalidTimeRangeParamProvider(): iterable
    {
        yield from [
            'missing_timestamps' => [[]],
            'missing_from_timestamp' => [['to' => 0]],
            'missing_to_timestamp' => [['from' => 0]],
            'non_numeric_timestamps' => [['from' => 'from', 'to' => 0]],
        ];
    }

    public function test_list_bike_locations_returns_404_response_for_unknown_bike(): void
    {
        $bikeId = Uuid::v4()->toRfc4122();

        $this->client->request('GET', "/bikes/bike/{$bikeId}/locations?from=0&to=1");

        self::assertResponseStatusCodeSame(404);
    }
}
