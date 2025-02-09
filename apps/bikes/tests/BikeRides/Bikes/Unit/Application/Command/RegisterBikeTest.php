<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeHandler;
use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use BikeRides\Foundation\Domain\CorrelationId;

final class RegisterBikeTest extends CommandTestCase
{
    public function test_it_registers_a_bike(): void
    {
        $correlationId = CorrelationId::generate();

        $handler = new RegisterBikeHandler($this->bikeRepository);
        $handler(new RegisterBikeCommand($correlationId->toString()));

        $bike = $this->bikeRepository->getByRegistrationCorrelationId($correlationId);
        self::assertEquals(new Bike($bike->bikeId, $correlationId, isActive: false), $bike);
    }
}
