<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetailsFetcher;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentAlreadyExists as RidePaymentAlreadyExistsDomainException;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentDuplicateChecker;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\SharedKernel\Domain\Event\RidePaymentInitiated;

final readonly class InitiateRidePaymentHandler implements CommandHandler
{
    public function __construct(
        private RidePaymentRepository $ridePaymentRepository,
        private RidePaymentDuplicateChecker $duplicateChecker,
        private RideDetailsFetcher $rideDetailsFetcher,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(InitiateRidePaymentCommand $command): void
    {
        try {
            $ridePayment = RidePayment::initiate(
                $command->ridePaymentId,
                $command->rideId,
                $this->rideDetailsFetcher,
                $this->duplicateChecker,
            );
        } catch (RidePaymentAlreadyExistsDomainException $exception) {
            throw RidePaymentAlreadyExists::fromDomainException($exception);
        }

        $this->ridePaymentRepository->store($ridePayment);

        $this->eventBus->publish(
            new RidePaymentInitiated(
                $ridePayment->getAggregateId()->toString(),
                $ridePayment->getRideId()->toString(),
            ),
        );
    }
}
