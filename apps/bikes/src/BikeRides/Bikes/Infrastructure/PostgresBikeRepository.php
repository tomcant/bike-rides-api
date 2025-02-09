<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use Doctrine\DBAL\Connection;

final readonly class PostgresBikeRepository implements BikeRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(Bike $bike): void
    {
        if (null === $bike->bikeId) {
            $this->connection->executeStatement(
                '
                    INSERT INTO bikes.bikes (registration_correlation_id, is_active)
                    VALUES (:registration_correlation_id, :is_active)
                ',
                self::mapObjectToRecord($bike),
            );

            return;
        }

        $this->connection->executeStatement(
            '
                UPDATE bikes.bikes
                SET is_active = :is_active,
                    registration_correlation_id = :registration_correlation_id
                WHERE bike_id = :bike_id
            ',
            self::mapObjectToRecord($bike),
        );
    }

    public function getById(BikeId $bikeId): Bike
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM bikes.bikes WHERE bike_id = :bike_id',
            ['bike_id' => $bikeId->toInt()],
        );

        if (false === $record) {
            throw BikeNotFound::forBikeId($bikeId);
        }

        return self::mapRecordToObject($record);
    }

    public function getByRegistrationCorrelationId(CorrelationId $correlationId): Bike
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM bikes.bikes WHERE registration_correlation_id = :registration_correlation_id',
            ['registration_correlation_id' => $correlationId->toString()],
        );

        if (false === $record) {
            throw BikeNotFound::forRegistrationCorrelationId($correlationId);
        }

        return self::mapRecordToObject($record);
    }

    public function list(): array
    {
        return \array_map(
            self::mapRecordToObject(...),
            $this->connection->fetchAllAssociative('SELECT * FROM bikes.bikes ORDER BY bike_id ASC'),
        );
    }

    /**
     * @param array{
     *   bike_id: int,
     *   registration_correlation_id: string,
     *   is_active: bool,
     * } $record
     */
    private static function mapRecordToObject(array $record): Bike
    {
        return new Bike(
            BikeId::fromInt($record['bike_id']),
            CorrelationId::fromString($record['registration_correlation_id']),
            $record['is_active'],
        );
    }

    /**
     * @return array{
     *   bike_id: ?int,
     *   registration_correlation_id: string,
     *   is_active: string,
     * }
     */
    private static function mapObjectToRecord(Bike $bike): array
    {
        return [
            'bike_id' => $bike->bikeId?->toInt(),
            'registration_correlation_id' => $bike->registrationCorrelationId->toString(),
            'is_active' => $bike->isActive ? 'true' : 'false',
        ];
    }
}
