<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderCommand;
use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderHandler;
use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Shared\Domain\Model\RiderId;

final class CreateRiderTest extends CommandTestCase
{
    private CreateRiderHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CreateRiderHandler($this->riderRepository);
    }

    public function test_it_creates_a_rider(): void
    {
        $riderId = RiderId::fromString('rider_id');

        ($this->handler)(new CreateRiderCommand($riderId->toString()));

        self::assertEquals(new Rider($riderId), $this->riderRepository->getById($riderId));
    }
}
