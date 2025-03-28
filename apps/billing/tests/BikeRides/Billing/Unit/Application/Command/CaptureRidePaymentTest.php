<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Command;

use App\BikeRides\Billing\Application\Command\CaptureRidePayment\CaptureRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\CaptureRidePayment\CaptureRidePaymentHandler;
use App\BikeRides\Billing\Application\Command\CaptureRidePayment\RidePaymentAlreadyCaptured;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentHandler;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentEventFactory;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use App\Tests\BikeRides\Billing\Doubles\RideDetailsFetcherStub;
use App\Tests\BikeRides\Billing\Doubles\RidePaymentDuplicateCheckerStub;
use App\Tests\BikeRides\Billing\Doubles\RidePaymentGatewayStub;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusDummy;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\InMemoryEventStore;
use BikeRides\Foundation\Domain\TransactionBoundaryDummy;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class CaptureRidePaymentTest extends CommandTestCase
{
    private RidePaymentRepository $ridePaymentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ridePaymentRepository = new RidePaymentRepository(
            new InMemoryEventStore(new RidePaymentEventFactory()),
        );
    }

    public function test_it_captures_a_ride_payment(): void
    {
        $this->initiateRidePayment(
            $ridePaymentId = RidePaymentId::generate(),
            RideId::generate(),
        );

        $handler = new CaptureRidePaymentHandler(
            $this->ridePaymentRepository,
            new RidePaymentGatewayStub($externalPaymentRef = 'external_payment_ref'),
        );
        $handler(new CaptureRidePaymentCommand($ridePaymentId->toString()));

        self::assertSame(
            $externalPaymentRef,
            $this->ridePaymentRepository->getById($ridePaymentId)->getExternalPaymentRef()->toString(),
        );
    }

    public function test_it_cannot_capture_a_ride_payment_that_has_already_been_captured(): void
    {
        $this->initiateRidePayment(
            $ridePaymentId = RidePaymentId::generate(),
            RideId::generate(),
        );

        $handler = new CaptureRidePaymentHandler(
            $this->ridePaymentRepository,
            new RidePaymentGatewayStub('external_payment_ref'),
        );
        $handler(new CaptureRidePaymentCommand($ridePaymentId->toString()));

        self::expectException(RidePaymentAlreadyCaptured::class);
        self::expectExceptionMessage(\sprintf("Ride payment ID '%s' has already been captured", $ridePaymentId->toString()));

        $handler(new CaptureRidePaymentCommand($ridePaymentId->toString()));
    }

    private function initiateRidePayment(RidePaymentId $ridePaymentId, RideId $rideId): void
    {
        $rideDetails = new RideDetails(
            RideDuration::fromStartAndEnd(
                new \DateTimeImmutable('-1 minute'),
                Clock::now(),
            ),
        );

        $handler = new InitiateRidePaymentHandler(
            $this->ridePaymentRepository,
            new RidePaymentDuplicateCheckerStub(isDuplicate: false),
            new RideDetailsFetcherStub($rideDetails),
            new TransactionBoundaryDummy(),
            new DomainEventBusDummy(),
        );

        $handler(new InitiateRidePaymentCommand($ridePaymentId->toString(), $rideId->toString()));
    }
}
