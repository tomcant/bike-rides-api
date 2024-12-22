<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface;

use App\Tests\BikeRides\Shared\Functional\UserInterface\UserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Model\Location;

abstract class BikesUserInterfaceTestCase extends UserInterfaceTestCase
{
    /** @return array<mixed, mixed> */
    protected function registerBike(): array
    {
        $response = $this->postJson('/bikes/bike');

        return $this->retrieveBike($response['bike_id']);
    }

    /** @return array<mixed, mixed> */
    protected function retrieveBike(string $bikeId): array
    {
        return $this->getJson("/bikes/bike/{$bikeId}");
    }

    protected function activateBike(string $bikeId): void
    {
        $this->postJson("/bikes/bike/{$bikeId}/activate");
    }

    protected function recordTrackingEvent(string $bikeId, Location $location): void
    {
        $this->postJson(
            '/bikes/tracking',
            [
                'bike_id' => $bikeId,
                'location' => $location->toArray(),
            ],
        );
    }

    /** @return array<mixed, mixed> */
    protected function listTrackingEvents(string $bikeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->getJson("/bikes/tracking?bike_id={$bikeId}&from={$from->getTimestamp()}&to={$to->getTimestamp()}");
    }
}
