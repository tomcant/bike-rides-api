<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\StoreRider\StoreRiderCommand;
use App\BikeRides\Rides\Application\Command\StoreRider\StoreRiderHandler;
use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;

final class StoreRiderTest extends CommandTestCase
{
    private StoreRiderHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new StoreRiderHandler($this->riderRepository);
    }

    public function test_it_stores_a_rider(): void
    {
        $riderId = RiderId::fromString('rider_id');

        ($this->handler)(new StoreRiderCommand($riderId->toString()));

        self::assertEquals(new Rider($riderId), $this->riderRepository->getById($riderId));
    }
}
