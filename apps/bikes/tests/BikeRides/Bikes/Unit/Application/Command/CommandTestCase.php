<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeCommand;
use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeHandler;
use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventCommand;
use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventHandler;
use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeHandler;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryBikeRepository;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryTrackingEventRepository;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusDummy;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Clock\ClockStub;
use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\TestCase;

abstract class CommandTestCase extends TestCase
{
    protected BikeRepository $bikeRepository;
    protected TrackingEventRepository $trackingEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bikeRepository = new InMemoryBikeRepository();
        $this->trackingEventRepository = new InMemoryTrackingEventRepository();

        Clock::useClock(new ClockStub());
    }

    protected function registerBike(BikeId $bikeId): void
    {
        $handler = new RegisterBikeHandler($this->bikeRepository);
        $handler(new RegisterBikeCommand($bikeId->toString()));
    }

    protected function recordTrackingEvent(BikeId $bikeId, Location $location): void
    {
        $handler = new RecordTrackingEventHandler($this->trackingEventRepository);
        $handler(new RecordTrackingEventCommand($bikeId->toString(), $location, Clock::now()));
    }

    protected function activateBike(BikeId $bikeId): void
    {
        $handler = new ActivateBikeHandler(
            $this->bikeRepository,
            $this->trackingEventRepository,
            new DomainEventBusDummy(),
        );
        $handler(new ActivateBikeCommand($bikeId->toString()));
    }

    protected static function assertDomainEventEquals(DomainEvent $expected, DomainEvent $actual): void
    {
        self::assertEquals($expected->serialize(), $actual->serialize());
    }
}
