<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Application\Query;

use App\BikeRides\Billing\Application\Query\ListRidePaymentsByRideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentWasCaptured;
use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentWasInitiated;
use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePriceCalculator;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjector;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvents;
use App\BikeRides\Shared\Domain\Helpers\AggregateVersion;
use App\BikeRides\Shared\Domain\Model\RideDuration;
use App\Tests\BikeRides\Billing\Doubles\InMemoryRidePaymentProjectionRepository;
use PHPUnit\Framework\TestCase;

final class ListRidePaymentsByRideIdTest extends TestCase
{
    private ListRidePaymentsByRideId $query;
    private RidePaymentProjector $projector;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryRidePaymentProjectionRepository();
        $this->query = new ListRidePaymentsByRideId($repository);
        $this->projector = new RidePaymentProjector($repository);
    }

    public function test_it_can_list_ride_payments_by_ride_id(): void
    {
        $ridePaymentId = RidePaymentId::generate();
        $rideId = RideId::fromString('ride_id');

        $ridePrice = (new RidePriceCalculator())
            ->calculatePrice(
                RideDuration::fromDateTimes(
                    new \DateTimeImmutable('-1 minute'),
                    new \DateTimeImmutable('now'),
                ),
            );

        $initiatedAt = new \DateTimeImmutable('now');
        $capturedAt = new \DateTimeImmutable('now');
        $externalPaymentRef = ExternalPaymentRef::fromString('ref');

        $eventStream = (new AggregateEvents([]))
            ->add(
                new RidePaymentWasInitiated(
                    $version = AggregateVersion::zero(),
                    $ridePaymentId,
                    $rideId,
                    $ridePrice,
                    $initiatedAt,
                ),
            )
            ->add(
                new RidePaymentWasCaptured(
                    $version->next(),
                    $ridePaymentId,
                    $externalPaymentRef,
                    $capturedAt,
                ),
            );

        ($this->projector)($eventStream);

        $ridePayments = $this->query->query($rideId->toString());

        self::assertCount(1, $ridePayments);
        self::assertSame($ridePaymentId->toString(), $ridePayments[0]['ride_payment_id']);
        self::assertSame($rideId->toString(), $ridePayments[0]['ride_id']);
        self::assertSame($initiatedAt, $ridePayments[0]['initiated_at']);
        self::assertSame($capturedAt, $ridePayments[0]['captured_at']);
        self::assertSame($externalPaymentRef->toString(), $ridePayments[0]['external_payment_ref']);
    }

    public function test_no_ride_payments_are_found_when_given_an_unknown_ride_id(): void
    {
        $rideId = RideId::fromString('ride_id');

        $ridePayments = $this->query->query($rideId->toString());

        self::assertEmpty($ridePayments);
    }
}
