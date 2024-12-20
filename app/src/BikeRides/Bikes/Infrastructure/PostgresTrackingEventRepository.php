<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Json;
use App\Foundation\Location;
use App\Foundation\Timestamp;
use Doctrine\DBAL\Connection;

final readonly class PostgresTrackingEventRepository implements TrackingEventRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(TrackingEvent $event): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO bikes.tracking (bike_id, location, tracked_at)
                VALUES (:bike_id, :location, :tracked_at)
            ',
            self::mapObjectToRecord($event),
        );
    }

    public function getLastEventForBikeId(BikeId $bikeId): ?TrackingEvent
    {
        $record = $this->connection->fetchAssociative(
            '
                SELECT *
                FROM bikes.tracking
                WHERE bike_id = :bike_id
                ORDER BY tracked_at DESC
                LIMIT 1
            ',
            ['bike_id' => $bikeId->toString()],
        );

        if (false === $record) {
            return null;
        }

        return self::mapRecordToObject($record);
    }

    public function getBetweenForBikeId(BikeId $bikeId, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $records = $this->connection->fetchAllAssociative(
            '
                SELECT *
                FROM bikes.tracking
                WHERE bike_id = :bike_id
                  AND tracked_at BETWEEN :from AND :to
            ',
            [
                'bike_id' => $bikeId->toString(),
                'from' => Timestamp::format($from),
                'to' => Timestamp::format($to),
            ],
        );

        return \array_map(self::mapRecordToObject(...), $records);
    }

    private static function mapRecordToObject(array $record): TrackingEvent
    {
        return new TrackingEvent(
            BikeId::fromString($record['bike_id']),
            Location::fromArray(Json::decode($record['location'])),
            Timestamp::from($record['tracked_at']),
        );
    }

    private static function mapObjectToRecord(TrackingEvent $event): array
    {
        return [
            'bike_id' => $event->bikeId->toString(),
            'location' => Json::encode($event->location->toArray()),
            'tracked_at' => Timestamp::format($event->trackedAt),
        ];
    }
}
