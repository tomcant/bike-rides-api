<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure\Postgres;

use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummary;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryNotFound;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryProjectionRepository;
use App\BikeRides\Shared\Domain\Model\RideDuration;
use Doctrine\DBAL\Connection;

final readonly class PostgresRideSummaryProjectionRepository implements RideSummaryProjectionRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(RideSummary $summary): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO rides.projection_ride_summary (ride_id, duration, route)
                VALUES (:ride_id, :duration, :route)
                ON CONFLICT (ride_id) DO UPDATE
                    SET duration = :duration,
                        route = :route
            ',
            self::mapObjectToRecord($summary),
        );
    }

    public function getByRideId(string $rideId): RideSummary
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rides.projection_ride_summary WHERE ride_id = :ride_id',
            ['ride_id' => $rideId],
        );

        if (false === $record) {
            throw new RideSummaryNotFound($rideId);
        }

        return self::mapRecordToObject($record);
    }

    private static function mapRecordToObject(array $record): RideSummary
    {
        return new RideSummary(
            $record['ride_id'],
            RideDuration::fromArray(\json_decode_array($record['duration'])),
            \json_decode_array($record['route']),
        );
    }

    private static function mapObjectToRecord(RideSummary $summary): array
    {
        return [
            'ride_id' => $summary->rideId,
            'duration' => \json_encode_array($summary->duration->toArray()),
            'route' => \json_encode_array($summary->route),
        ];
    }
}
