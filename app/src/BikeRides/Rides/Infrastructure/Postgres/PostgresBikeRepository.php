<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure\Postgres;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
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
            ['bike_id' => $bikeId->toString()],
        );

        if (false === $record) {
            throw new BikeNotFound($bikeId);
        }

        return self::mapRecordToObject($record);
    }

    public function list(): array
    {
        return \array_map(
            self::mapRecordToObject(...),
            $this->connection->fetchAllAssociative('SELECT * FROM rides.bikes'),
        );
    }

    private static function mapRecordToObject(array $record): Bike
    {
        return new Bike(
            BikeId::fromString($record['bike_id']),
            $record['location'] ? Location::fromArray(\json_decode_array($record['location'])) : null,
        );
    }

    private static function mapObjectToRecord(Bike $bike): array
    {
        return [
            'bike_id' => $bike->bikeId->toString(),
            'location' => $bike->location ? \json_encode_array($bike->location->toArray()) : null,
        ];
    }
}
