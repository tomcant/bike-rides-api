<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\UpdateBikeLocation\UpdateBikeLocationCommand;
use App\BikeRides\Rides\Application\Command\UpdateBikeLocation\UpdateBikeLocationHandler;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\Foundation\Location;

final class UpdateBikeLocationTest extends CommandTestCase
{
    public function test_it_updates_bike_location(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $location = new Location(0, 0);

        $handler = new UpdateBikeLocationHandler($this->bikeRepository);
        $handler(new UpdateBikeLocationCommand($bikeId->toString(), $location));

        self::assertEquals($location, $this->bikeRepository->getById($bikeId)->location);
    }
}
