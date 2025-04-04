<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\RegisterBike;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;

final readonly class RegisterBikeHandler implements CommandHandler
{
    public function __construct(private BikeRepository $repository)
    {
    }

    public function __invoke(RegisterBikeCommand $command): void
    {
        $bike = Bike::register($command->correlationId);

        $this->repository->store($bike);
    }
}
