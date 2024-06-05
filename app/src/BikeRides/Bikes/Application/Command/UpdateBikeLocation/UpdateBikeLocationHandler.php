<?php declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\UpdateBikeLocation;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class UpdateBikeLocationHandler implements CommandHandler
{
    public function __construct(private BikeRepository $bikeRepository)
    {
    }

    public function __invoke(UpdateBikeLocationCommand $command): void
    {
        $bike = $this->bikeRepository->getById($command->bikeId);

        $bike->locate($command->location);

        $this->bikeRepository->store($bike);
    }
}
