<?php

declare(strict_types=1);

namespace BikeRides\Rides\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Rides\Functional\UserInterface\RidesUserInterfaceTestCase;
use Symfony\Component\Uid\Uuid;

final class BikeRetrievalTest extends RidesUserInterfaceTestCase
{
    public function test_retrieving_a_bike(): void
    {
        ['bike_id' => $bikeId] = $this->createBike();

        $bike = $this->getJson("/rides/bike/{$bikeId}");

        self::assertArrayHasKeys($bike, ['_links', 'bike_id', 'location']);
        self::assertArrayHasKeys($bike['_links'], ['self', 'start-ride']);
        self::assertSame($bikeId, $bike['bike_id']);
    }

    public function test_retrieving_a_bike_returns_a_404_response_for_an_unknown_bike(): void
    {
        $bikeId = Uuid::v4()->toRfc4122();

        $this->getJson("/rides/bike/{$bikeId}", assertResponseIsSuccessful: false);

        self::assertResponseStatusCodeSame(404);
    }
}
