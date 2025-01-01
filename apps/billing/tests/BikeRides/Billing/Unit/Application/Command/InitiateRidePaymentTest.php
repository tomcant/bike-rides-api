<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Command;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentHandler;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\RidePaymentAlreadyExists;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentEventFactory;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePrice;
use App\Tests\BikeRides\Billing\Doubles\RideDetailsFetcherStub;
use App\Tests\BikeRides\Billing\Doubles\RidePaymentDuplicateCheckerStub;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusDummy;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;
use BikeRides\Foundation\Domain\InMemoryEventStore;
use BikeRides\Foundation\Domain\TransactionBoundaryDummy;
use BikeRides\SharedKernel\Domain\Event\RidePaymentInitiated;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use Money\Money;

final class InitiateRidePaymentTest extends CommandTestCase
{
    public function test_it_initiates_a_ride_payment(): void
    {
        $rideDurationInMinutes = 10;
        $pricePerMinuteInPence = 25;

        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::fromString('ride_id');

        $rideDetails = new RideDetails(
            $rideDuration = RideDuration::fromStartAndEnd(
                $startedAt = (new \DateTimeImmutable())->setTime(hour: 12, minute: 30),
                $startedAt->modify('+' . $rideDurationInMinutes . ' minutes'),
            ),
        );

        $handler = new InitiateRidePaymentHandler(
            $ridePaymentRepository = new RidePaymentRepository(
                new InMemoryEventStore(new RidePaymentEventFactory()),
            ),
            new RidePaymentDuplicateCheckerStub(isDuplicate: false),
            new RideDetailsFetcherStub($rideDetails),
            new TransactionBoundaryDummy(),
            $eventBus = new DomainEventBusSpy(),
        );
        $handler(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));

        $ridePayment = $ridePaymentRepository->getById($ridePaymentId);

        self::assertEquals($ridePaymentId, $ridePayment->getAggregateId());
        self::assertEquals($rideId, $ridePayment->getRideId());

        self::assertEquals(
            new RidePrice(
                Money::GBP($rideDurationInMinutes * $pricePerMinuteInPence),
                Money::GBP($pricePerMinuteInPence),
                $rideDuration,
            ),
            $ridePayment->getRidePrice(),
        );

        self::assertDomainEventEquals(
            new RidePaymentInitiated(
                ridePaymentId: $ridePaymentId->toString(),
                rideId: $rideId->toString(),
            ),
            $eventBus->lastEvent,
        );
    }

    public function test_it_deduplicates_ride_payments(): void
    {
        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::fromString('ride_id');

        $rideDetails = new RideDetails(
            RideDuration::fromStartAndEnd(
                new \DateTimeImmutable('now'),
                new \DateTimeImmutable('+10 seconds'),
            ),
        );

        $handler = new InitiateRidePaymentHandler(
            new RidePaymentRepository(new InMemoryEventStore(new RidePaymentEventFactory())),
            $duplicateChecker = new RidePaymentDuplicateCheckerStub(isDuplicate: false),
            new RideDetailsFetcherStub($rideDetails),
            new TransactionBoundaryDummy(),
            new DomainEventBusDummy(),
        );
        $handler(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));

        $duplicateChecker->setIsDuplicate(true);

        self::expectException(RidePaymentAlreadyExists::class);
        self::expectExceptionMessage(\sprintf("Duplicate payment for ride ID '%s'", $rideId->toString()));

        $handler(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));
    }
}
