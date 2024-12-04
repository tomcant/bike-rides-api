<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\UserInterface;

use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Shared\Domain\Model\RiderId;

final class RiderApiTest extends RidesUiTestCase
{
    public function test_create_rider(): void
    {
        $riderId = 'rider_id';

        $this->postJson('/rides/rider', ['rider_id' => $riderId]);

        $rider = self::getContainer()->get(RiderRepository::class)->getById(RiderId::fromString($riderId));
        self::assertEquals($riderId, $rider->riderId->toString());
    }
}
