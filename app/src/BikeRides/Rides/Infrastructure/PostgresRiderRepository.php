<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Rider\Rider;
use App\BikeRides\Rides\Domain\Model\Rider\RiderNotFound;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use Doctrine\DBAL\Connection;

final readonly class PostgresRiderRepository implements RiderRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(Rider $rider): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO rides.riders (rider_id)
                VALUES (:rider_id)
                ON CONFLICT (rider_id) DO NOTHING
            ',
            self::mapObjectToRecord($rider),
        );
    }

    public function getById(RiderId $riderId): Rider
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rides.riders WHERE rider_id = :rider_id',
            ['rider_id' => $riderId->toString()],
        );

        if (false === $record) {
            throw new RiderNotFound($riderId);
        }

        return self::mapRecordToObject($record);
    }

    private static function mapRecordToObject(array $record): Rider
    {
        return new Rider(RiderId::fromString($record['rider_id']));
    }

    private static function mapObjectToRecord(Rider $rider): array
    {
        return ['rider_id' => $rider->riderId->toString()];
    }
}
