<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\UserInterface;

use App\BikeRides\Shared\Domain\Event\BikeActivated;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\UserInterface\UiTestCase;

abstract class RidesUiTestCase extends UiTestCase
{
    protected function startRide(string $riderId, string $bikeId): array
    {
        $bike = $this->getJson('/rides/bike/' . $bikeId);

        $response = $this->postJson($bike['_links']['start-ride']['href'], ['rider_id' => $riderId]);

        return $this->retrieveRide($response['ride_id']);
    }

    protected function endRide(string $rideId): void
    {
        $ride = $this->getJson('/rides/ride/' . $rideId);

        $this->postJson($ride['_links']['end']['href']);
    }

    protected function retrieveRide(string $rideId): array
    {
        return $this->getJson('/rides/ride/' . $rideId);
    }

    protected function createBike(): array
    {
        $bikeId = BikeId::generate()->toString();

        $this->publishEvent(new BikeActivated($bikeId, new Location(0, 0)));

        return $this->retrieveBike($bikeId);
    }

    protected function retrieveBike(string $bikeId): array
    {
        return $this->getJson('/rides/bike/' . $bikeId);
    }

    protected function createRider(): array
    {
        $riderId = \uniqid('rider_');

        $this->postJson('/rides/rider', ['rider_id' => $riderId]);

        return ['rider_id' => $riderId];
    }
}
