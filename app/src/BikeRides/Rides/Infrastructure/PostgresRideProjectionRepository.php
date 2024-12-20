<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Projection\Ride\Ride;
use App\BikeRides\Rides\Domain\Projection\Ride\RideNotFound;
use App\BikeRides\Rides\Domain\Projection\Ride\RideProjectionRepository;
use App\Foundation\Timestamp;
use Doctrine\DBAL\Connection;

final readonly class PostgresRideProjectionRepository implements RideProjectionRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(Ride $ride): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO rides.projection_ride (ride_id, rider_id, bike_id, started_at, ended_at)
                VALUES (:ride_id, :rider_id, :bike_id, :started_at, :ended_at)
                ON CONFLICT (ride_id) DO UPDATE
                    SET rider_id = :rider_id,
                        bike_id = :bike_id,
                        started_at = :started_at,
                        ended_at = :ended_at
            ',
            self::mapObjectToRecord($ride),
        );
    }

    public function getById(string $rideId): Ride
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rides.projection_ride WHERE ride_id = :ride_id',
            ['ride_id' => $rideId],
        );

        if (false === $record) {
            throw new RideNotFound($rideId);
        }

        return self::mapRecordToObject($record);
    }

    private static function mapRecordToObject(array $record): Ride
    {
        return new Ride(
            $record['ride_id'],
            $record['rider_id'],
            $record['bike_id'],
            Timestamp::from($record['started_at']),
            Timestamp::fromNullable($record['ended_at']),
        );
    }

    private static function mapObjectToRecord(Ride $ride): array
    {
        return [
            'ride_id' => $ride->rideId,
            'rider_id' => $ride->riderId,
            'bike_id' => $ride->bikeId,
            'started_at' => Timestamp::format($ride->startedAt),
            'ended_at' => Timestamp::formatNullable($ride->endedAt),
        ];
    }
}
