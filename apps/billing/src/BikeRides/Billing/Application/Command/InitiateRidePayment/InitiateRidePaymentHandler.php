<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\Foundation\Domain\TransactionBoundary;
use BikeRides\SharedKernel\Domain\Event\RidePaymentInitiated;

final readonly class InitiateRidePaymentHandler implements CommandHandler
{
    public function __construct(
        private RidePaymentRepository $ridePaymentRepository,
        private RidePaymentDuplicateChecker $duplicateChecker,
        private RideDetailsFetcher $rideDetailsFetcher,
        private TransactionBoundary $transaction,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(InitiateRidePaymentCommand $command): void
    {
        if ($this->duplicateChecker->isDuplicate($command->rideId)) {
            throw new RidePaymentAlreadyInitiated($command->rideId);
        }

        $rideDetails = $this->rideDetailsFetcher->fetch($command->rideId);
        $ridePayment = RidePayment::initiate($command->ridePaymentId, $command->rideId, $rideDetails);

        $this->transaction->begin();

        try {
            $this->ridePaymentRepository->store($ridePayment);

            $this->eventBus->publish(
                new RidePaymentInitiated(
                    $ridePayment->getAggregateId()->toString(),
                    $ridePayment->getRideId()->toString(),
                    $ridePayment->getRidePrice()->toArray(),
                ),
            );
        } catch (\Throwable $exception) {
            $this->transaction->abort();

            throw $exception;
        }

        $this->transaction->end();
    }
}
