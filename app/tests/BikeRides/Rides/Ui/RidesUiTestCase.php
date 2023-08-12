<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Ui;

use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Ui\UiTestCase;

abstract class RidesUiTestCase extends UiTestCase
{
    protected function storeRider(string $riderId): void
    {
        $this->postJson('/rides/rider', ['rider_id' => $riderId]);
    }

    protected function registerBike(): array
    {
        return $this->postJson('/rides/bike');
    }

    protected function trackBike(string $bikeId, Location $location): void
    {
        $this->postJson(
            '/bike/track',
            [
                'bike_id' => $bikeId,
                'location' => $location->toArray(),
            ],
        );

        $this->clock->tick();
    }

    protected function startRide(string $riderId, string $bikeId): array
    {
        $bike = $this->getJson('/rides/bike/' . $bikeId);

        return $this->postJson($bike['_links']['start-ride']['href'], ['rider_id' => $riderId]);
    }

    protected function endRide(string $rideId): void
    {
        $ride = $this->getJson('/rides/ride/' . $rideId);

        $this->postJson($ride['_links']['end']['href']);
    }
}
