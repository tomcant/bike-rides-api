<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
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
                INSERT INTO rides.bikes (bike_id, location)
                VALUES (:bike_id, :location)
                ON CONFLICT (bike_id) DO UPDATE
                  SET location = :location
            ',
            self::mapObjectToRecord($bike),
        );
    }

    public function getById(BikeId $bikeId): Bike
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rides.bikes WHERE bike_id = :bike_id',
            ['bike_id' => $bikeId->toInt()],
        );

        if (false === $record) {
            throw new BikeNotFound($bikeId);
        }

        return self::mapRecordToObject($record);
    }

    public function remove(BikeId $bikeId): void
    {
        $rowCount = $this->connection->executeStatement(
            'DELETE FROM rides.bikes WHERE bike_id = :bike_id',
            ['bike_id' => $bikeId->toInt()],
        );

        if (1 !== $rowCount) {
            throw new BikeNotFound($bikeId);
        }
    }

    public function list(): array
    {
        return \array_map(
            self::mapRecordToObject(...),
            $this->connection->fetchAllAssociative('SELECT * FROM rides.bikes'),
        );
    }

    /**
     * @param array{
     *   bike_id: int,
     *   location: ?string,
     * } $record
     */
    private static function mapRecordToObject(array $record): Bike
    {
        return new Bike(
            BikeId::fromInt($record['bike_id']),
            Location::fromArray(Json::decode($record['location'])),
        );
    }

    /**
     * @return array{
     *   bike_id: int,
     *   location: ?string,
     * }
     */
    private static function mapObjectToRecord(Bike $bike): array
    {
        return [
            'bike_id' => $bike->bikeId->toInt(),
            'location' => Json::encode($bike->location->toArray()),
        ];
    }
}
