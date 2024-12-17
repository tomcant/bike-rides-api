<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface;

use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Functional\UserInterface\UserInterfaceTestCase;

abstract class BikesUserInterfaceTestCase extends UserInterfaceTestCase
{
    protected function registerBike(): array
    {
        $response = $this->postJson('/bikes/bike');

        return $this->retrieveBike($response['bike_id']);
    }

    protected function retrieveBike(string $bikeId): array
    {
        return $this->getJson('/bikes/bike/' . $bikeId);
    }

    protected function activateBike(string $bikeId, Location $location): void
    {
        $this->postJson(
            "/bikes/bike/{$bikeId}/activate",
            ['location' => $location->toArray()],
        );
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

    protected function listTrackingEvents(string $bikeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->getJson("/bikes/tracking?bikeId={$bikeId}&from={$from->getTimestamp()}&to={$to->getTimestamp()}");
    }
}
