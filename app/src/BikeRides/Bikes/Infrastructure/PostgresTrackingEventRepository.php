<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Json;
use App\Foundation\Location;
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
                'from' => \datetime_timestamp($from),
                'to' => \datetime_timestamp($to),
            ],
        );

        return \array_map(self::mapRecordToObject(...), $records);
    }

    private static function mapRecordToObject(array $record): TrackingEvent
    {
        return new TrackingEvent(
            BikeId::fromString($record['bike_id']),
            Location::fromArray(Json::decode($record['location'])),
            new \DateTimeImmutable($record['tracked_at']),
        );
    }

    private static function mapObjectToRecord(TrackingEvent $event): array
    {
        return [
            'bike_id' => $event->bikeId->toString(),
            'location' => Json::encode($event->location->toArray()),
            'tracked_at' => \datetime_timestamp($event->trackedAt),
        ];
    }
}
