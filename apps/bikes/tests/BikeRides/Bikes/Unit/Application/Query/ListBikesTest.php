<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Query;

use App\BikeRides\Bikes\Application\Query\ListBikes;
use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryBikeRepository;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryTrackingEventRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\TestCase;

final class ListBikesTest extends TestCase
{
    private ListBikes $query;
    private InMemoryBikeRepository $bikeRepository;
    private InMemoryTrackingEventRepository $trackingEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new ListBikes(
            $this->bikeRepository = new InMemoryBikeRepository(),
            $this->trackingEventRepository = new InMemoryTrackingEventRepository(),
        );
    }

    public function test_it_can_list_bikes(): void
    {
        $this->bikeRepository->store($bike1 = new Bike(BikeId::generate(), isActive: false));
        $this->bikeRepository->store($bike2 = new Bike(BikeId::generate(), isActive: true));
        $this->trackingEventRepository->store(new TrackingEvent($bike2->bikeId, new Location(0, 0), Clock::now()));
        $this->trackingEventRepository->store(new TrackingEvent($bike2->bikeId, new Location(1, 1), Clock::now()));

        $bikes = $this->query->query();

        self::assertCount(2, $bikes);
        self::assertSame($bike1->bikeId->toString(), $bikes[0]['bike_id']);
        self::assertSame($bike2->bikeId->toString(), $bikes[1]['bike_id']);
        self::assertFalse($bikes[0]['is_active']);
        self::assertTrue($bikes[1]['is_active']);
        self::assertNull($bikes[0]['location']);
        self::assertSame((new Location(1, 1))->toArray(), $bikes[1]['location']);
    }
}
