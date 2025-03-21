<?php

declare(strict_types=1);

namespace BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\AddPriceToSummary\AddPriceToSummaryCommand;
use App\BikeRides\Rides\Application\Command\AddPriceToSummary\AddPriceToSummaryHandler;
use App\Tests\BikeRides\Rides\Unit\Application\Command\CommandTestCase;
use BikeRides\Foundation\Money\Money;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final class AddPriceToSummaryTest extends CommandTestCase
{
    private AddPriceToSummaryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new AddPriceToSummaryHandler($this->summaryRepository);
    }

    public function test_it_adds_the_price_to_the_summary(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::fromInt(1));
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);
        $this->endRide($rideId);
        $this->summariseRide($rideId);

        ($this->handler)(new AddPriceToSummaryCommand($rideId->toString(), $price = Money::GBP(100)));

        $summary = $this->summaryRepository->getByRideId($rideId);
        self::assertEquals($price, $summary->price);
    }

    public function test_it_cannot_add_the_price_before_the_ride_is_summarised(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::fromInt(1));
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);
        $this->endRide($rideId);

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage("Could not add price to summary for ride ID '{$rideId->toString()}'");

        ($this->handler)(new AddPriceToSummaryCommand($rideId->toString(), Money::GBP(100)));
    }
}
