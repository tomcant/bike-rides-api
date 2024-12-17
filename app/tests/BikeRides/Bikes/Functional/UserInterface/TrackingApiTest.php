<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface;

use App\Foundation\Clock\Clock;
use App\Foundation\Location;
use Symfony\Component\Uid\Uuid;

final class TrackingApiTest extends BikesUserInterfaceTestCase
{
    public function test_it_records_a_tracking_event(): void
    {
        $bike = $this->registerBike();
        $fromTimestamp = Clock::now();

        $this->postJson(
            '/bikes/tracking',
            [
                'bike_id' => $bike['bike_id'],
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
            ],
        );

        $events = $this->listTrackingEvents(
            bikeId: $bike['bike_id'],
            from: $fromTimestamp,
            to: Clock::now(),
        );

        self::assertCount(1, $events['_embedded']['tracking_event']);
        self::assertEquals($location, $events['_embedded']['tracking_event'][0]['location']);
    }

    /** @dataProvider invalidLocationProvider */
    public function test_tracking_returns_a_400_response_for_an_invalid_location(array $location): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            '/bikes/tracking',
            ['bike_id' => $bike['bike_id'], ...$location],
            assertResponseIsSuccessful: false,
        );

        self::assertResponseStatusCodeSame(400);
    }

    public static function invalidLocationProvider(): iterable
    {
        yield from [
            'missing_location' => [[]],
            'missing_longitude' => [['location' => ['latitude' => 0]]],
            'missing_latitude' => [['location' => ['longitude' => 0]]],
        ];
    }

    public function test_the_current_bike_location_is_updated_when_a_tracking_event_is_recorded(): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            '/bikes/tracking',
            [
                'bike_id' => $bike['bike_id'],
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
            ],
        );

        $currentBikeLocation = $this->retrieveBike($bike['bike_id'])['location'];
        self::assertEquals($location, $currentBikeLocation);
    }

    public function test_it_lists_tracking_events(): void
    {
        $bike = $this->registerBike();
        $fromTimestamp = Clock::now()->getTimestamp();
        $this->recordTrackingEvent($bike['bike_id'], $locationOne = new Location(0, 0));
        $this->recordTrackingEvent($bike['bike_id'], $locationTwo = new Location(1, 1));
        $this->recordTrackingEvent($bike['bike_id'], $locationThree = new Location(2, 2));
        $toTimestamp = Clock::now()->getTimestamp();

        $list = $this->getJson("/bikes/tracking?bikeId={$bike['bike_id']}&from={$fromTimestamp}&to={$toTimestamp}");

        self::assertCount(3, $list['_embedded']['tracking_event']);
        self::assertSame(3, $list['total']);

        foreach ($list['_embedded']['tracking_event'] as $event) {
            self::assertArrayHasKeys($event, ['location', 'trackedAt']);
            self::assertIsNumeric($event['trackedAt']);
        }

        self::assertEquals($locationOne->toArray(), $list['_embedded']['tracking_event'][0]['location']);
        self::assertEquals($locationTwo->toArray(), $list['_embedded']['tracking_event'][1]['location']);
        self::assertEquals($locationThree->toArray(), $list['_embedded']['tracking_event'][2]['location']);
    }

    /** @dataProvider invalidTimeRangeProvider */
    public function test_list_tracking_events_returns_400_response_for_invalid_time_range(array $timeRange): void
    {
        $bike = $this->registerBike();
        $queryString = \http_build_query(['bikeId' => $bike['bike_id'], ...$timeRange]);

        $this->client->request('GET', "/bikes/tracking?{$queryString}");

        self::assertResponseStatusCodeSame(400);
    }

    public static function invalidTimeRangeProvider(): iterable
    {
        yield from [
            'missing_timestamps' => [[]],
            'missing_from_timestamp' => [['to' => 0]],
            'missing_to_timestamp' => [['from' => 0]],
            'non_numeric_timestamps' => [['from' => 'from', 'to' => 0]],
        ];
    }

    public function test_list_tracking_events_returns_404_response_for_unknown_bike(): void
    {
        $bikeId = Uuid::v4()->toRfc4122();

        $this->client->request('GET', "/bikes/tracking?bikeId={$bikeId}&from=0&to=1");

        self::assertResponseStatusCodeSame(404);
    }
}
