<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Ui;

use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use App\BikeRides\Rides\Domain\Model\Track\TrackRepository;
use App\Foundation\Location;

final class RideApiTest extends RidesUiTestCase
{
    public function test_bike_registration(): void
    {
        $registration = $this->postJson('/rides/bike');
        $bikeUrl = $this->parseResponseLinkUrl();

        self::assertNotNull($registration['bike_id'] ?? null);
        self::assertStringContainsString($registration['bike_id'], $bikeUrl);
    }

    public function test_retrieve_bike(): void
    {
        ['bike_id' => $bikeId] = $this->registerBike();
        $bikeUrl = $this->parseResponseLinkUrl();

        $bike = $this->getJson($bikeUrl);

        self::assertArrayHasKeys($bike, ['_links', 'bike_id', 'location']);
        self::assertArrayHasKeys($bike['_links'], ['self', 'start-ride']);

        self::assertSame($bikeId, $bike['bike_id']);
    }

    public function test_list_bikes(): void
    {
        ['bike_id' => $bikeId1] = $this->registerBike();
        ['bike_id' => $bikeId2] = $this->registerBike();

        $list = $this->getJson('/rides/bike');

        self::assertCount(2, $list['_links']['bike']);
        self::assertCount(2, $list['_embedded']['bike']);
        self::assertSame(2, $list['total']);

        foreach ($list['_embedded']['bike'] as $bike) {
            self::assertArrayHasKeys($bike, ['_links', 'bike_id', 'location']);
            self::assertArrayHasKeys($bike['_links'], ['self', 'start-ride']);
        }

        self::assertSame($bikeId1, $list['_embedded']['bike'][0]['bike_id']);
        self::assertSame($bikeId2, $list['_embedded']['bike'][1]['bike_id']);
    }

    public function test_bike_tracking(): void
    {
        $this->registerBike();
        $bikeUrl = $this->parseResponseLinkUrl();
        $bike = $this->getJson($bikeUrl);

        self::assertNull($bike['location']);

        $this->postJson(
            '/bike/track',
            [
                'bike_id' => $bike['bike_id'],
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
            ],
        );

        $trackEvents = self::getContainer()->get(TrackRepository::class)
            ->getBetweenForBikeId(
                new \DateTimeImmutable('-1 minute'),
                new \DateTimeImmutable('now'),
                BikeId::fromString($bike['bike_id']),
            );

        self::assertCount(1, $trackEvents);

        self::assertSame($location, $this->getJson($bikeUrl)['location']);
    }

    /** @dataProvider invalidLocationProvider */
    public function test_location_is_required_for_tracking(array $location): void
    {
        $this->registerBike();

        $this->postJson(
            '/bike/track',
            ['bike_id' => 'bike_id', ...$location],
            assertResponseIsSuccessful: false,
        );

        self::assertResponseStatusCodeSame(400);
    }

    public function invalidLocationProvider(): array
    {
        return [
            'missing_location' => [[]],
            'missing_longitude' => [['location' => ['latitude' => 0]]],
            'missing_latitude' => [['location' => ['longitude' => 0]]],
        ];
    }

    public function test_start_ride(): void
    {
        $this->storeRider($riderId = 'rider_id');
        $this->registerBike();

        $bike = $this->getJson($this->parseResponseLinkUrl());

        $startRide = $this->postJson($bike['_links']['start-ride']['href'], ['rider_id' => $riderId]);

        self::assertResponseStatusCodeSame(201);

        $rideUrl = $this->parseResponseLinkUrl();

        self::assertNotNull($startRide['ride_id'] ?? null);
        self::assertStringContainsString($startRide['ride_id'], $rideUrl);

        $ride = $this->getJson($rideUrl);

        self::assertIsNumeric($ride['started_at']);
        self::assertNull($ride['ended_at']);
    }

    public function test_it_cannot_start_a_ride_if_the_bike_is_not_available(): void
    {
        $this->storeRider($riderId = 'rider_id');
        $this->registerBike();

        $bike = $this->getJson($this->parseResponseLinkUrl());
        $this->startRide($riderId, $bike['bike_id']);

        $response = $this->postJson(
            $bike['_links']['start-ride']['href'],
            ['rider_id' => $riderId],
            assertResponseIsSuccessful: false,
        );

        self::assertResponseStatusCodeSame(400);
        self::assertSame(['error' => \sprintf('Bike "%s" is not available', $bike['bike_id'])], $response);
    }

    public function test_retrieve_ride(): void
    {
        $this->storeRider($riderId = 'rider_id');
        ['bike_id' => $bikeId] = $this->registerBike();
        ['ride_id' => $rideId] = $this->startRide($riderId, $bikeId);

        $ride = $this->getJson($this->parseResponseLinkUrl());

        self::assertArrayHasKeys($ride, ['_links', 'ride_id', 'rider_id', 'bike_id', 'started_at', 'ended_at']);
        self::assertArrayHasKeys($ride['_links'], ['self', 'end']);

        self::assertSame($rideId, $ride['ride_id']);
        self::assertSame($riderId, $ride['rider_id']);
        self::assertSame($bikeId, $ride['bike_id']);
    }

    public function test_end_ride(): void
    {
        $this->storeRider($riderId = 'rider_id');
        ['bike_id' => $bikeId] = $this->registerBike();
        $this->startRide($riderId, $bikeId);
        $rideUrl = $this->parseResponseLinkUrl();
        $this->trackBike($bikeId, new Location(0, 0));

        $ride = $this->getJson($rideUrl);

        $this->postJson($ride['_links']['end']['href']);

        $ride = $this->getJson($rideUrl);

        self::assertGreaterThanOrEqual($ride['started_at'], $ride['ended_at']);
    }

    public function test_ride_summary(): void
    {
        $this->storeRider($riderId = 'rider_id');
        ['bike_id' => $bikeId] = $this->registerBike();
        ['ride_id' => $rideId] = $this->startRide($riderId, $bikeId);
        $rideUrl = $this->parseResponseLinkUrl();
        $this->trackBike($bikeId, new Location(0, 0));

        $ride = $this->getJson($rideUrl);
        self::assertArrayNotHasKey('summary', $ride['_links']);

        $this->endRide($rideId);

        $ride = $this->getJson($rideUrl);
        self::assertArrayHasKey('summary', $ride['_links']);

        $summary = $this->getJson($ride['_links']['summary']['href']);

        self::assertArrayHasKeys($summary, ['_links', 'ride_id', 'duration', 'route']);

        self::assertSame($rideId, $summary['ride_id']);

        self::assertGreaterThan($summary['duration']['started_at'], $summary['duration']['ended_at']);

        self::assertEquals(
            [
                $summary['duration']['started_at'] => [
                    'latitude' => 0,
                    'longitude' => 0,
                ],
            ],
            $summary['route'],
        );
    }

    public function test_store_rider(): void
    {
        $riderId = RiderId::fromString('rider_id');

        $this->postJson('/rides/rider', ['rider_id' => $riderId->toString()]);

        self::assertEquals(
            new Rider($riderId),
            self::getContainer()->get(RiderRepository::class)->getById($riderId),
        );
    }
}
