<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Command;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentHandler;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\RidePaymentAlreadyInitiated;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentEventFactory;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePrice;
use App\Tests\BikeRides\Billing\Doubles\RideDetailsFetcherStub;
use App\Tests\BikeRides\Billing\Doubles\RidePaymentDuplicateCheckerStub;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\InMemoryEventStore;
use BikeRides\Foundation\Domain\TransactionBoundaryDummy;
use BikeRides\SharedKernel\Domain\Event\RidePaymentInitiated;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Money\Money;

final class InitiateRidePaymentTest extends CommandTestCase
{
    private const int PRICE_PER_MINUTE_IN_PENCE = 25;
    private InitiateRidePaymentHandler $handler;
    private RidePaymentRepository $repository;
    private RidePaymentDuplicateCheckerStub $duplicateChecker;
    private RideDuration $rideDuration;
    private DomainEventBusSpy $eventBus;

    protected function setUp(): void
    {
        $this->rideDuration = RideDuration::fromStartAndEnd(
            ($endedAt = Clock::now())->modify('-10 minutes'),
            $endedAt,
        );

        $this->handler = new InitiateRidePaymentHandler(
            $this->repository = new RidePaymentRepository(new InMemoryEventStore(new RidePaymentEventFactory())),
            $this->duplicateChecker = new RidePaymentDuplicateCheckerStub(isDuplicate: false),
            new RideDetailsFetcherStub(new RideDetails($this->rideDuration)),
            new TransactionBoundaryDummy(),
            $this->eventBus = new DomainEventBusSpy(),
        );
    }

    public function test_it_initiates_a_ride_payment(): void
    {
        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::generate();

        ($this->handler)(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));

        $ridePayment = $this->repository->getById($ridePaymentId);
        self::assertEquals($rideId, $ridePayment->getRideId());
        self::assertEquals(
            new RidePrice(
                totalPrice: Money::GBP($this->rideDuration->minutes * self::PRICE_PER_MINUTE_IN_PENCE),
                pricePerMinute: Money::GBP(self::PRICE_PER_MINUTE_IN_PENCE),
                rideDuration: $this->rideDuration,
            ),
            $ridePayment->getRidePrice(),
        );
    }

    public function test_it_publishes_a_ride_payment_initiated_domain_event(): void
    {
        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::generate();

        ($this->handler)(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));

        self::assertDomainEventEquals(
            new RidePaymentInitiated($ridePaymentId->toString(), $rideId->toString()),
            $this->eventBus->lastEvent,
        );
    }

    public function test_it_does_not_initiaite_multiple_ride_payments_for_the_same_ride(): void
    {
        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::generate();

        ($this->handler)(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));

        $this->duplicateChecker->setIsDuplicate(true);

        self::expectException(RidePaymentAlreadyInitiated::class);
        self::expectExceptionMessage(\sprintf("Duplicate payment for ride ID '%s'", $rideId->toString()));

        ($this->handler)(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));
    }
}
