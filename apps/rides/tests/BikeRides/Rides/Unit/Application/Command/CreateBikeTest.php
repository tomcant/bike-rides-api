<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeCommand;
use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeHandler;
use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class CreateBikeTest extends CommandTestCase
{
    public function test_it_creates_a_bike(): void
    {
        $bikeId = BikeId::fromInt(1);
        $location = new Location(0, 0);

        $handler = new CreateBikeHandler($this->bikeRepository);
        $handler(new CreateBikeCommand($bikeId->toInt(), $location));

        $bike = $this->bikeRepository->getById($bikeId);
        self::assertEquals(new Bike($bikeId, $location), $bike);
    }
}
