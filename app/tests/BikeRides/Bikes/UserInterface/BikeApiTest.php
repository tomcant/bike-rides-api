<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\UserInterface;

final class BikeApiTest extends BikesUiTestCase
{
    public function test_register_bike(): void
    {
        ['bike_id' => $bikeId] = $this->postJson('/bikes/bike');

        self::assertNotNull($bikeId);
        self::assertResponseStatusCodeSame(201);

        $bikeUrl = $this->parseResponseLinkUrl();
        self::assertStringContainsString($bikeId, $bikeUrl);

        $bike = $this->getJson($bikeUrl);
        self::assertSame($bikeId, $bike['bike_id']);
        self::assertNull($bike['location']);
        self::assertFalse($bike['is_active']);
    }

    public function test_retrieve_bike(): void
    {
        $bike = $this->registerBike();

        $response = $this->getJson('/bikes/bike/' . $bike['bike_id']);

        self::assertArrayHasKeys($response, ['_links', 'bike_id', 'location', 'is_active']);
        self::assertArrayHasKeys($response['_links'], ['self', 'activate']);
        self::assertSame($bike['bike_id'], $response['bike_id']);
    }

    public function test_activate_bike(): void
    {
        $bike = $this->registerBike();

        $this->postJson(
            $bike['_links']['activate']['href'],
            [
                'location' => $location = [
                    'latitude' => 51.535704,
                    'longitude' => -0.126946,
                ],
            ],
        );

        $bike = $this->retrieveBike($bike['bike_id']);
        self::assertArrayNotHasKey('activate', $bike['_links']);
        self::assertEquals($location, $bike['location']);
        self::assertTrue($bike['is_active']);
    }
}
