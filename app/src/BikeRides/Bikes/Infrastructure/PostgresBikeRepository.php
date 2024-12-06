<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Json;
use App\Foundation\Location;
use Doctrine\DBAL\Connection;

final readonly class PostgresBikeRepository implements BikeRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(Bike $bike): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO bikes.bikes (bike_id, location, is_active)
                VALUES (:bike_id, :location, :is_active)
                ON CONFLICT (bike_id) DO UPDATE
                  SET location = :location,
                      is_active = :is_active
            ',
            self::mapObjectToRecord($bike),
        );
    }

    public function getById(BikeId $bikeId): Bike
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM bikes.bikes WHERE bike_id = :bike_id',
            ['bike_id' => $bikeId->toString()],
        );

        if (false === $record) {
            throw new BikeNotFound($bikeId);
        }

        return self::mapRecordToObject($record);
    }

    private static function mapRecordToObject(array $record): Bike
    {
        return new Bike(
            BikeId::fromString($record['bike_id']),
            $record['location'] ? Location::fromArray(Json::decode($record['location'])) : null,
            $record['is_active'],
        );
    }

    private static function mapObjectToRecord(Bike $bike): array
    {
        return [
            'bike_id' => $bike->bikeId->toString(),
            'location' => $bike->location ? Json::encode($bike->location->toArray()) : null,
            'is_active' => $bike->isActive ? 'true' : 'false',
        ];
    }
}
