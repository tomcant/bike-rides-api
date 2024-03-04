<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\CaptureRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentAlreadyCaptured as RidePaymentAlreadyCapturedDomainException;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentGateway;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class CaptureRidePaymentHandler implements CommandHandler
{
    public function __construct(
        private RidePaymentRepository $ridePaymentRepository,
        private RidePaymentGateway $ridePaymentGateway,
    ) {
    }

    public function __invoke(CaptureRidePaymentCommand $command): void
    {
        $ridePayment = $this->ridePaymentRepository->getById($command->ridePaymentId);

        try {
            $ridePayment->capture($this->ridePaymentGateway);
        } catch (RidePaymentAlreadyCapturedDomainException $exception) {
            throw RidePaymentAlreadyCaptured::fromDomainException($exception);
        }

        $this->ridePaymentRepository->store($ridePayment);
    }
}
