<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\RefreshBikeLocation;

use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;

final readonly class RefreshBikeLocationHandler implements CommandHandler
{
    public function __construct(
        private BikeRepository $bikeRepository,
        private BikeLocationFetcher $bikeLocationFetcher,
    ) {
    }

    public function __invoke(RefreshBikeLocationCommand $command): void
    {
        $location = $this->bikeLocationFetcher->fetch($command->bikeId);

        $bike = $this->bikeRepository->getById($command->bikeId);

        $bike->locate($location);

        $this->bikeRepository->store($bike);
    }
}
