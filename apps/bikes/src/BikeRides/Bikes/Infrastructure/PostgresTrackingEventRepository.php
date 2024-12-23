<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Timestamp;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
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

    /**
     * @param array{
     *   bike_id: string,
     *   location: string,
     *   tracked_at: string,
     * } $record
     */
    private static function mapRecordToObject(array $record): TrackingEvent
    {
        return new TrackingEvent(
            BikeId::fromString($record['bike_id']),
            Location::fromArray(Json::decode($record['location'])),
            Timestamp::from($record['tracked_at']),
        );
    }

    /**
     * @return array{
     *   bike_id: string,
     *   location: string,
     *   tracked_at: string,
     * }
     */
    private static function mapObjectToRecord(TrackingEvent $event): array
    {
        return [
            'bike_id' => $event->bikeId->toString(),
            'location' => Json::encode($event->location->toArray()),
            'tracked_at' => Timestamp::format($event->trackedAt),
        ];
    }
}
