<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Query;

use App\BikeRides\Bikes\Application\Query\GetBikeById;
use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryBikeRepository;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryTrackingEventRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\TestCase;

final class GetBikeByIdTest extends TestCase
{
    private GetBikeById $query;
    private InMemoryBikeRepository $bikeRepository;
    private InMemoryTrackingEventRepository $trackingEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new GetBikeById(
            $this->bikeRepository = new InMemoryBikeRepository(),
            $this->trackingEventRepository = new InMemoryTrackingEventRepository(),
        );
    }

    public function test_it_can_get_a_bike_by_id(): void
    {
        $this->bikeRepository->store(Bike::register($bikeId = BikeId::generate()));
        $this->trackingEventRepository->store(new TrackingEvent($bikeId, new Location(0, 0), Clock::now()));

        $bike = $this->query->query($bikeId->toString());

        self::assertSame($bikeId->toString(), $bike['bike_id']);
        self::assertFalse($bike['is_active']);
        self::assertSame((new Location(0, 0))->toArray(), $bike['location']);
    }

    public function test_no_bike_is_found_when_given_an_unknown_bike_id(): void
    {
        $bikeId = BikeId::generate();

        $bike = $this->query->query($bikeId->toString());

        self::assertNull($bike);
    }
}
