<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Application\Command;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentCommand;
use App\BikeRides\Billing\Application\Command\InitiateRidePayment\InitiateRidePaymentHandler;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentEventFactory;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentRepository;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePrice;
use App\BikeRides\Shared\Domain\Model\RideDuration;
use App\Tests\BikeRides\Billing\Doubles\RideDetailsFetcherStub;
use App\Tests\BikeRides\Shared\Doubles\InMemoryEventStore;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class InitiateRidePaymentTest extends TestCase
{
    public function test_it_initiates_a_ride_payment(): void
    {
        $rideDurationInMinutes = 10;
        $pricePerMinuteInPence = 25;

        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::fromString('ride_id');

        $rideDetails = new RideDetails(
            $rideDuration = RideDuration::fromDateTimes(
                $startedAt = (new \DateTimeImmutable())->setTime(hour: 12, minute: 30),
                $startedAt->modify('+' . $rideDurationInMinutes . ' minutes'),
            ),
        );

        $handler = new InitiateRidePaymentHandler(
            $ridePaymentRepository = new RidePaymentRepository(
                new InMemoryEventStore(new RidePaymentEventFactory()),
            ),
            new RideDetailsFetcherStub($rideDetails),
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
    }
}
