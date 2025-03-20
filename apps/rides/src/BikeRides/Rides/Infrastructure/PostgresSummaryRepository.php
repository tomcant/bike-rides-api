<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Summary\Route;
use App\BikeRides\Rides\Domain\Model\Summary\Summary;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryNotFound;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryRepository;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Model\Location;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Doctrine\DBAL\Connection;

final readonly class PostgresSummaryRepository implements SummaryRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(Summary $summary): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO rides.summaries (ride_id, duration, route, price)
                VALUES (:ride_id, :duration, :route, :price)
                ON CONFLICT (ride_id) DO UPDATE
                    SET duration = :duration,
                        route = :route,
                        price = :price
            ',
            self::mapObjectToRecord($summary),
        );
    }

    public function getByRideId(RideId $rideId): Summary
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM rides.summaries WHERE ride_id = :ride_id',
            ['ride_id' => $rideId->toString()],
        );

        if (false === $record) {
            throw SummaryNotFound::forRideId($rideId);
        }

        return self::mapRecordToObject($record);
    }

    /**
     * @param array{
     *   ride_id: string,
     *   duration: string,
     *   route: string,
     *   price: null|string,
     * } $record
     */
    private static function mapRecordToObject(array $record): Summary
    {
        return new Summary(
            RideId::fromString($record['ride_id']),
            RideDuration::fromArray(Json::decode($record['duration'])),
            new Route(\array_map(Location::fromArray(...), Json::decode($record['route']))),
            null !== $record['price'] ? \money_from_array(Json::decode($record['price'])) : null,
        );
    }

    /**
     * @return array{
     *   ride_id: string,
     *   duration: string,
     *   route: string,
     *   price: null|string,
     * }
     */
    private static function mapObjectToRecord(Summary $summary): array
    {
        return [
            'ride_id' => $summary->rideId->toString(),
            'duration' => Json::encode($summary->duration->toArray()),
            'route' => Json::encode($summary->route->toArray()),
            'price' => null !== $summary->price ? Json::encode($summary->price->jsonSerialize()) : null,
        ];
    }
}
