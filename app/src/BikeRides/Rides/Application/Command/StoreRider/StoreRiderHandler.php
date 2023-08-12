<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\StoreRider;

use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class StoreRiderHandler implements CommandHandler
{
    public function __construct(private RiderRepository $riderRepository)
    {
    }

    public function __invoke(StoreRiderCommand $command): void
    {
        $rider = new Rider($command->riderId);

        $this->riderRepository->store($rider);
    }
}
