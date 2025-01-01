<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeActivationTest extends BikesUserInterfaceTestCase
{
    public function test_activating_a_bike(): void
    {
        $bike = $this->registerBike();
        $this->recordTrackingEvent($bike['bike_id'], new Location(0, 0));

        $this->postJson($bike['_links']['activate']['href']);

        $bike = $this->retrieveBike($bike['bike_id']);
        self::assertArrayNotHasKey('activate', $bike['_links']);
        self::assertTrue($bike['is_active']);
    }

    public function test_a_tracking_event_is_required_to_activate_a_bike(): void
    {
        $bike = $this->registerBike();

        $response = $this->postJson($bike['_links']['activate']['href'], assertResponseIsSuccessful: false);

        self::assertResponseStatusCodeSame(500);
        self::assertSame("Could not activate bike with ID '{$bike['bike_id']}'. Reason: 'Bike has not been tracked'", $response['detail']);
    }
}
