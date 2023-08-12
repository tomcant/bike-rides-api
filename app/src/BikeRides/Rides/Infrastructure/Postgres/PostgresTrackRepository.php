<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure\Postgres;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Track\Track;
use App\BikeRides\Rides\Domain\Model\Track\TrackRepository;
use App\Foundation\Location;
use Doctrine\DBAL\Connection;

final readonly class PostgresTrackRepository implements TrackRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(Track $track): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO rides.tracks (bike_id, location, tracked_at)
                VALUES (:bike_id, :location, :tracked_at)
            ',
            self::mapObjectToRecord($track),
        );
    }

    public function getBetweenForBikeId(
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        BikeId $bikeId,
    ): array {
        $records = $this->connection->fetchAllAssociative(
            '
                SELECT *
                FROM rides.tracks
                WHERE bike_id = :bike_id
                  AND tracked_at BETWEEN :from AND :to
            ',
            [
                'bike_id' => $bikeId->toString(),
                'from' => \datetime_timestamp($from),
                'to' => \datetime_timestamp($to),
            ],
        );

        return \array_map(self::mapRecordToObject(...), $records);
    }

    private static function mapRecordToObject(array $record): Track
    {
        return new Track(
            BikeId::fromString($record['bike_id']),
            Location::fromArray(\json_decode_array($record['location'])),
            new \DateTimeImmutable($record['tracked_at']),
        );
    }

    private static function mapObjectToRecord(Track $track): array
    {
        return [
            'bike_id' => $track->bikeId->toString(),
            'location' => \json_encode_array($track->location->toArray()),
            'tracked_at' => \datetime_timestamp($track->trackedAt),
        ];
    }
}
