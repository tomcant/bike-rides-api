<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeHandler;
use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Shared\Domain\Model\BikeId;

final class RegisterBikeTest extends CommandTestCase
{
    public function test_it_registers_a_bike(): void
    {
        $bikeId = BikeId::generate();

        $handler = new RegisterBikeHandler($this->bikeRepository);
        $handler(new RegisterBikeCommand($bikeId->toString()));

        self::assertEquals(Bike::register($bikeId), $this->bikeRepository->getById($bikeId));
    }
}
