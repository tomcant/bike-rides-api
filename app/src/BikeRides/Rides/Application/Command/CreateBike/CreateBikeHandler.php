<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\CreateBike;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class CreateBikeHandler implements CommandHandler
{
    public function __construct(private BikeRepository $bikeRepository)
    {
    }

    public function __invoke(CreateBikeCommand $command): void
    {
        $bike = new Bike($command->bikeId, $command->location);

        $this->bikeRepository->store($bike);
    }
}
