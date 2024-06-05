<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\CreateRider;

use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class CreateRiderHandler implements CommandHandler
{
    public function __construct(private RiderRepository $riderRepository)
    {
    }

    public function __invoke(CreateRiderCommand $command): void
    {
        $rider = new Rider($command->riderId);

        $this->riderRepository->store($rider);
    }
}
