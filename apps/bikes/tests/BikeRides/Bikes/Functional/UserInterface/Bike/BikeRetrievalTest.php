<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeRetrievalTest extends BikesUserInterfaceTestCase
{
    public function test_retrieving_a_bike(): void
    {
        $bike = $this->registerBike();
        $this->recordTrackingEvent($bike['bike_id'], new Location(0, 0));
        $bike = $this->retrieveBike($bike['bike_id']);

        $response = $this->getJson("/bike/{$bike['bike_id']}");

        self::assertArrayHasKeys($response, ['_links', 'bike_id', 'is_active']);
        self::assertArrayHasKeys($response['_links'], ['self', 'activate']);
        self::assertSame($bike['bike_id'], $response['bike_id']);
        self::assertFalse($bike['is_active']);
        self::assertEquals((new Location(0, 0))->toArray(), $bike['location']);
    }

    public function test_retrieving_a_bike_returns_a_404_response_for_an_unknown_bike(): void
    {
        $this->getJson('/bike/1', assertResponseIsSuccessful: false);

        self::assertResponseStatusCodeSame(404);
    }
}
