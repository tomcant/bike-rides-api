<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Query;

use App\BikeRides\Bikes\Application\Query\ListBikes;
use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryBikeRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use PHPUnit\Framework\TestCase;

final class ListBikesTest extends TestCase
{
    private ListBikes $query;
    private InMemoryBikeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new ListBikes(
            $this->repository = new InMemoryBikeRepository(),
        );
    }

    public function test_it_can_list_bikes(): void
    {
        $this->repository->store(
            $bike1 = new Bike(BikeId::generate(), isActive: false),
        );
        $this->repository->store(
            $bike2 = new Bike(BikeId::generate(), isActive: true),
        );

        $bikes = $this->query->query();

        self::assertCount(2, $bikes);
        self::assertSame($bike1->bikeId->toString(), $bikes[0]['bike_id']);
        self::assertSame($bike2->bikeId->toString(), $bikes[1]['bike_id']);
        self::assertEquals($bike1->isActive, $bikes[0]['is_active']);
        self::assertEquals($bike2->isActive, $bikes[1]['is_active']);
    }
}
