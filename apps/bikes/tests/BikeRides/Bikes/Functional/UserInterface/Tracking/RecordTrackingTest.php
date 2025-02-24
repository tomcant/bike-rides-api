<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Tracking;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\Attributes\DataProvider;

final class RecordTrackingTest extends BikesUserInterfaceTestCase
{
    public function test_it_records_a_tracking_event(): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            '/tracking',
            [
                'bike_id' => $bike['bike_id'],
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
                'tracked_at' => Clock::now()->getTimestamp(),
            ],
        );

        $events = $this->listTrackingEvents(
            bikeId: $bike['bike_id'],
            from: Clock::now()->modify('-1 minute'),
            to: Clock::now(),
        );
        self::assertCount(1, $events['_embedded']['tracking_event']);
        self::assertEquals($location, $events['_embedded']['tracking_event'][0]['location']);
    }

    /** @param array{location?: array{latitude?: int, longitude?: int}} $location */
    #[DataProvider('invalidLocationProvider')]
    public function test_recording_a_tracking_event_returns_a_400_response_for_an_invalid_location(array $location): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            '/tracking',
            ['bike_id' => $bike['bike_id'], ...$location],
            assertResponseIsSuccessful: false,
        );

        self::assertResponseStatusCodeSame(400);
    }

    /** @return iterable<array{location?: array{latitude?: int, longitude?: int}}> */
    public static function invalidLocationProvider(): iterable
    {
        yield from [
            'missing_location' => [[]],
            'missing_longitude' => [['location' => ['latitude' => 0]]],
            'missing_latitude' => [['location' => ['longitude' => 0]]],
        ];
    }
}
