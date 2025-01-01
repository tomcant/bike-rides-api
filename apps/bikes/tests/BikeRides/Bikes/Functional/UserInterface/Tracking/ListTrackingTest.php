<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Tracking;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Uid\Uuid;

final class ListTrackingTest extends BikesUserInterfaceTestCase
{
    public function test_it_lists_tracking_events(): void
    {
        $bike = $this->registerBike();
        $fromTimestamp = Clock::now()->getTimestamp();
        $this->recordTrackingEvent($bike['bike_id'], $locationOne = new Location(0, 0));
        $this->recordTrackingEvent($bike['bike_id'], $locationTwo = new Location(1, 1));
        $this->recordTrackingEvent($bike['bike_id'], $locationThree = new Location(2, 2));
        $toTimestamp = Clock::now()->getTimestamp();

        $list = $this->getJson("/tracking?bike_id={$bike['bike_id']}&from={$fromTimestamp}&to={$toTimestamp}");

        self::assertCount(3, $list['_embedded']['tracking_event']);
        self::assertSame(3, $list['total']);

        foreach ($list['_embedded']['tracking_event'] as $event) {
            self::assertArrayHasKeys($event, ['location', 'tracked_at']);
            self::assertIsNumeric($event['tracked_at']);
        }

        self::assertEquals($locationOne->toArray(), $list['_embedded']['tracking_event'][0]['location']);
        self::assertEquals($locationTwo->toArray(), $list['_embedded']['tracking_event'][1]['location']);
        self::assertEquals($locationThree->toArray(), $list['_embedded']['tracking_event'][2]['location']);
    }

    /** @param list<array{from?: mixed, to?: mixed}> $timeRange */
    #[DataProvider('invalidTimeRangeProvider')]
    public function test_listing_tracking_events_returns_a_400_response_for_an_invalid_time_range(array $timeRange): void
    {
        $bike = $this->registerBike();
        $queryString = \http_build_query(['bike_id' => $bike['bike_id'], ...$timeRange]);

        $this->getJson("/tracking?{$queryString}", assertResponseIsSuccessful: false);

        self::assertResponseStatusCodeSame(400);
    }

    /** @return iterable<array{from?: mixed, to?: mixed}> */
    public static function invalidTimeRangeProvider(): iterable
    {
        yield from [
            'missing_timestamps' => [[]],
            'missing_from_timestamp' => [['to' => 0]],
            'missing_to_timestamp' => [['from' => 0]],
            'non_numeric_timestamp' => [['from' => 'from', 'to' => 0]],
        ];
    }

    public function test_listing_tracking_events_returns_a_404_response_for_an_unknown_bike(): void
    {
        $bikeId = Uuid::v4()->toRfc4122();

        $this->getJson("/tracking?bike_id={$bikeId}&from=0&to=1", assertResponseIsSuccessful: false);

        self::assertResponseStatusCodeSame(404);
    }
}
