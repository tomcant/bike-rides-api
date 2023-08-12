<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetailsFetcher;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class InitiateRidePaymentHandler implements CommandHandler
{
    public function __construct(
        private RidePaymentRepository $ridePaymentRepository,
        private RideDetailsFetcher $rideDetailsFetcher,
    ) {
    }

    public function __invoke(InitiateRidePaymentCommand $command): void
    {
        $ridePayment = RidePayment::initiate(
            $command->ridePaymentId,
            $command->rideId,
            $this->rideDetailsFetcher,
        );

        $this->ridePaymentRepository->store($ridePayment);
    }
}
