<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface;

use BikeRides\SharedKernel\Domain\Event\BikeActivated;
use BikeRides\SharedKernel\Domain\Model\Location;

abstract class RidesUserInterfaceTestCase extends UserInterfaceTestCase
{
    /** @return array<mixed, mixed> */
    protected function startRide(string $riderId, int $bikeId): array
    {
        $bike = $this->getJson("/bike/{$bikeId}");

        $response = $this->postJson($bike['_links']['start-ride']['href'], ['rider_id' => $riderId]);

        return $this->retrieveRide($response['ride_id']);
    }

    protected function endRide(string $rideId): void
    {
        $ride = $this->getJson("/ride/{$rideId}");

        $this->postJson($ride['_links']['end']['href']);
    }

    /** @return array<mixed, mixed> */
    protected function retrieveRide(string $rideId): array
    {
        return $this->getJson("/ride/{$rideId}");
    }

    /** @return array<mixed, mixed> */
    protected function createBike(): array
    {
        $bikeId = \random_int(1, 1_000_000);

        $this->handleDomainEvent(new BikeActivated($bikeId, new Location(0, 0)));

        return $this->retrieveBike($bikeId);
    }

    /** @return array<mixed, mixed> */
    protected function retrieveBike(int $bikeId): array
    {
        return $this->getJson("/bike/{$bikeId}");
    }

    /** @return array{rider_id: string} */
    protected function createRider(): array
    {
        $riderId = \uniqid('rider_');

        $this->postJson('/rider', ['rider_id' => $riderId]);

        return ['rider_id' => $riderId];
    }
}
