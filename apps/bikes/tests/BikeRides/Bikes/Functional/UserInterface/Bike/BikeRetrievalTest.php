<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use Symfony\Component\Uid\Uuid;

final class BikeRetrievalTest extends BikesUserInterfaceTestCase
{
    public function test_retrieving_a_bike(): void
    {
        $bike = $this->registerBike();

        $response = $this->getJson("/bike/{$bike['bike_id']}");

        self::assertArrayHasKeys($response, ['_links', 'bike_id', 'is_active']);
        self::assertArrayHasKeys($response['_links'], ['self', 'activate']);
        self::assertSame($bike['bike_id'], $response['bike_id']);
    }

    public function test_retrieving_a_bike_returns_a_404_response_for_an_unknown_bike(): void
    {
        $bikeId = Uuid::v4()->toRfc4122();

        $this->getJson("/bike/{$bikeId}", assertResponseIsSuccessful: false);

        self::assertResponseStatusCodeSame(404);
    }
}
