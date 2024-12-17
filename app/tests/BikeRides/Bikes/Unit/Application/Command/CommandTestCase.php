<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeCommand;
use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeHandler;
use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeHandler;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryTrackingEventRepository;
use App\Tests\BikeRides\Bikes\Unit\Doubles\InMemoryBikeRepository;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusDummy;
use App\Tests\BikeRides\Shared\Unit\Application\Command\CommandTestCase as BaseCommandTestCase;

abstract class CommandTestCase extends BaseCommandTestCase
{
    protected readonly BikeRepository $bikeRepository;
    protected readonly TrackingEventRepository $trackingEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bikeRepository = new InMemoryBikeRepository();
        $this->trackingEventRepository = new InMemoryTrackingEventRepository();
    }

    protected function registerBike(BikeId $bikeId): void
    {
        $handler = new RegisterBikeHandler($this->bikeRepository);
        $handler(new RegisterBikeCommand($bikeId->toString()));
    }

    protected function activateBike(BikeId $bikeId, Location $location): void
    {
        $handler = new ActivateBikeHandler($this->bikeRepository, new DomainEventBusDummy());
        $handler(new ActivateBikeCommand($bikeId->toString(), $location));
    }
}
