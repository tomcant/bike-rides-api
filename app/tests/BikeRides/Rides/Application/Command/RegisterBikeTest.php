<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Rides\Application\Command\RegisterBike\RegisterBikeHandler;
use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;

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
