<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\RemoveBike;

use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;

final readonly class RemoveBikeHandler implements CommandHandler
{
    public function __construct(private BikeRepository $bikeRepository)
    {
    }

    public function __invoke(RemoveBikeCommand $command): void
    {
        $this->bikeRepository->remove($command->bikeId);
    }
}
