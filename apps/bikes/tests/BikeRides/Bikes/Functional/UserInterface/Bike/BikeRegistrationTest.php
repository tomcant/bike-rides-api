<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;

final class BikeRegistrationTest extends BikesUserInterfaceTestCase
{
    public function test_registering_a_bike(): void
    {
        ['bike_id' => $bikeId] = $this->postJson('/bike');

        self::assertNotNull($bikeId);
        self::assertResponseStatusCodeSame(201);

        $bikeUrl = $this->parseResponseLinkUrl();
        self::assertStringContainsString($bikeId, $bikeUrl);

        $bike = $this->getJson($bikeUrl);
        self::assertSame($bikeId, $bike['bike_id']);
        self::assertFalse($bike['is_active']);
    }
}
