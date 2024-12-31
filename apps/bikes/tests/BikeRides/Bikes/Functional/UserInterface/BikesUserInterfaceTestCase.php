<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface;

use BikeRides\SharedKernel\Domain\Model\Location;

abstract class BikesUserInterfaceTestCase extends UserInterfaceTestCase
{
    /** @return array<mixed, mixed> */
    protected function registerBike(): array
    {
        $response = $this->postJson('/bike');

        return $this->retrieveBike($response['bike_id']);
    }

    /** @return array<mixed, mixed> */
    protected function retrieveBike(string $bikeId): array
    {
        return $this->getJson("/bike/{$bikeId}");
    }

    protected function activateBike(string $bikeId): void
    {
        $this->postJson("/bike/{$bikeId}/activate");
    }

    protected function recordTrackingEvent(string $bikeId, Location $location): void
    {
        $this->postJson(
            '/tracking',
            [
                'bike_id' => $bikeId,
                'location' => $location->toArray(),
            ],
        );
    }

    /** @return array<mixed, mixed> */
    protected function listTrackingEvents(string $bikeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->getJson("/tracking?bike_id={$bikeId}&from={$from->getTimestamp()}&to={$to->getTimestamp()}");
    }
}
