<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\AddPriceToSummary;

use App\BikeRides\Rides\Domain\Model\Summary\SummaryNotFound;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;

final readonly class AddPriceToSummaryHandler implements CommandHandler
{
    public function __construct(
        private SummaryRepository $summaryRepository,
    ) {
    }

    public function __invoke(AddPriceToSummaryCommand $command): void
    {
        try {
            $summary = $this->summaryRepository->getByRideId($command->rideId);
        } catch (SummaryNotFound) {
            throw new \RuntimeException("Could not add price to summary for ride ID '{$command->rideId->toString()}'");
        }

        $summary->price = $command->price;

        $this->summaryRepository->store($summary);
    }
}
