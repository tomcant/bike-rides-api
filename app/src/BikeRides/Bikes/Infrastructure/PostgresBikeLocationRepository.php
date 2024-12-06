<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocation;
use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocationRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Json;
use App\Foundation\Location;
use Doctrine\DBAL\Connection;

final readonly class PostgresBikeLocationRepository implements BikeLocationRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(BikeLocation $bikeLocation): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO bikes.bike_locations (bike_id, location, located_at)
                VALUES (:bike_id, :location, :located_at)
            ',
            self::mapObjectToRecord($bikeLocation),
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
                FROM bikes.bike_locations
                WHERE bike_id = :bike_id
                  AND located_at BETWEEN :from AND :to
            ',
            [
                'bike_id' => $bikeId->toString(),
                'from' => \datetime_timestamp($from),
                'to' => \datetime_timestamp($to),
            ],
        );

        return \array_map(self::mapRecordToObject(...), $records);
    }

    private static function mapRecordToObject(array $record): BikeLocation
    {
        return new BikeLocation(
            BikeId::fromString($record['bike_id']),
            Location::fromArray(Json::decode($record['location'])),
            new \DateTimeImmutable($record['located_at']),
        );
    }

    private static function mapObjectToRecord(BikeLocation $bikeLocation): array
    {
        return [
            'bike_id' => $bikeLocation->bikeId->toString(),
            'location' => Json::encode($bikeLocation->location->toArray()),
            'located_at' => \datetime_timestamp($bikeLocation->locatedAt),
        ];
    }
}
