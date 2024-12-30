<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderCommand;
use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderHandler;
use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final class CreateRiderTest extends CommandTestCase
{
    public function test_it_creates_a_rider(): void
    {
        $riderId = RiderId::fromString('rider_id');

        $handler = new CreateRiderHandler($this->riderRepository);
        $handler(new CreateRiderCommand($riderId->toString()));

        $rider = $this->riderRepository->getById($riderId);
        self::assertEquals(new Rider($riderId), $rider);
    }
}
