<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Rider\RiderNotFound;
use App\BikeRides\Rides\Infrastructure\PostgresRiderRepository;
use App\BikeRides\Shared\Domain\Model\RiderId;
use App\Tests\BikeRides\Shared\Infrastructure\PostgresTestCase;

final class PostgresRiderRepositoryTest extends PostgresTestCase
{
    private PostgresRiderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresRiderRepository($this->connection);
    }

    public function test_it_stores_a_rider(): void
    {
        $rider = new Rider(RiderId::fromString('rider_id'));

        $this->repository->store($rider);

        self::assertEquals($rider, $this->repository->getById($rider->riderId));
    }

    public function test_unable_to_get_by_unknown_id(): void
    {
        self::expectException(RiderNotFound::class);

        $this->repository->getById(RiderId::fromString('rider_id'));
    }
}
