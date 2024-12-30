<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface;

use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeApiTest extends BikesUserInterfaceTestCase
{
    public function test_registering_a_bike(): void
    {
        ['bike_id' => $bikeId] = $this->postJson('/bikes/bike');

        self::assertNotNull($bikeId);
        self::assertResponseStatusCodeSame(201);

        $bikeUrl = $this->parseResponseLinkUrl();
        self::assertStringContainsString($bikeId, $bikeUrl);

        $bike = $this->getJson($bikeUrl);
        self::assertSame($bikeId, $bike['bike_id']);
        self::assertFalse($bike['is_active']);
    }

    public function test_retrieving_bike(): void
    {
        $bike = $this->registerBike();

        $response = $this->getJson("/bikes/bike/{$bike['bike_id']}");

        self::assertArrayHasKeys($response, ['_links', 'bike_id', 'is_active']);
        self::assertArrayHasKeys($response['_links'], ['self', 'activate']);
        self::assertSame($bike['bike_id'], $response['bike_id']);
    }

    public function test_activating_bike(): void
    {
        $bike = $this->registerBike();
        $this->recordTrackingEvent($bike['bike_id'], new Location(0, 0));

        $this->postJson($bike['_links']['activate']['href']);

        $bike = $this->retrieveBike($bike['bike_id']);
        self::assertArrayNotHasKey('activate', $bike['_links']);
        self::assertTrue($bike['is_active']);
    }

    public function test_it_cannot_activate_a_bike_before_recording_a_tracking_event(): void
    {
        self::markTestSkipped();
    }
}
