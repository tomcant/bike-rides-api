<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeCommand;
use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeHandler;
use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

final class CreateBikeTest extends CommandTestCase
{
    public function test_it_creates_a_bike(): void
    {
        $bikeId = BikeId::generate();
        $location = new Location(0, 0);

        $handler = new CreateBikeHandler($this->bikeRepository);
        $handler(new CreateBikeCommand($bikeId->toString(), $location));

        self::assertEquals(
            new Bike($bikeId, $location),
            $this->bikeRepository->getById($bikeId),
        );
    }
}
