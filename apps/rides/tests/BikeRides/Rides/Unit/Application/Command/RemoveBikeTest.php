<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\RemoveBike\RemoveBikeCommand;
use App\BikeRides\Rides\Application\Command\RemoveBike\RemoveBikeHandler;
use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class RemoveBikeTest extends CommandTestCase
{
    public function test_it_removes_a_bike(): void
    {
        $this->createBike($bikeId = BikeId::generate());

        $handler = new RemoveBikeHandler($this->bikeRepository);
        $handler(new RemoveBikeCommand($bikeId->toString()));

        self::expectException(BikeNotFound::class);
        $this->bikeRepository->getById($bikeId);
    }
}
