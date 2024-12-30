<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface;

use BikeRides\SharedKernel\Domain\Event\BikeActivated;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

abstract class RidesUserInterfaceTestCase extends UserInterfaceTestCase
{
    /** @return array<mixed, mixed> */
    protected function startRide(string $riderId, string $bikeId): array
    {
        $bike = $this->getJson("/rides/bike/{$bikeId}");

        $response = $this->postJson($bike['_links']['start-ride']['href'], ['rider_id' => $riderId]);

        return $this->retrieveRide($response['ride_id']);
    }

    protected function endRide(string $rideId): void
    {
        $ride = $this->getJson("/rides/ride/{$rideId}");

        $this->postJson($ride['_links']['end']['href']);
    }

    /** @return array<mixed, mixed> */
    protected function retrieveRide(string $rideId): array
    {
        return $this->getJson("/rides/ride/{$rideId}");
    }

    /** @return array<mixed, mixed> */
    protected function createBike(): array
    {
        $bikeId = BikeId::generate()->toString();

        $this->handleDomainEvent(new BikeActivated($bikeId, new Location(0, 0)));

        return $this->retrieveBike($bikeId);
    }

    /** @return array<mixed, mixed> */
    protected function retrieveBike(string $bikeId): array
    {
        return $this->getJson("/rides/bike/{$bikeId}");
    }

    /** @return array{rider_id: string} */
    protected function createRider(): array
    {
        $riderId = \uniqid('rider_');

        $this->postJson('/rides/rider', ['rider_id' => $riderId]);

        return ['rider_id' => $riderId];
    }
}
