<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentNotFound;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Money\Money;
use BikeRides\Foundation\Timestamp;
use Doctrine\DBAL\Connection;

final readonly class PostgresRidePaymentProjectionRepository implements RidePaymentProjectionRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function store(RidePayment $ridePayment): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO billing.projection_ride_payment
                    (ride_payment_id, ride_id, total_price, price_per_minute, initiated_at, captured_at, external_payment_ref)
                VALUES
                    (:ride_payment_id, :ride_id, :total_price, :price_per_minute, :initiated_at, :captured_at, :external_payment_ref)
                ON CONFLICT (ride_payment_id) DO UPDATE
                    SET ride_id = :ride_id,
                        total_price = :total_price,
                        price_per_minute = :price_per_minute,
                        initiated_at = :initiated_at,
                        captured_at = :captured_at,
                        external_payment_ref = :external_payment_ref
            ',
            self::mapObjectToRecord($ridePayment),
        );
    }

    public function getById(string $ridePaymentId): RidePayment
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM billing.projection_ride_payment WHERE ride_payment_id = :ride_payment_id',
            ['ride_payment_id' => $ridePaymentId],
        );

        if (false === $record) {
            throw RidePaymentNotFound::forRidePaymentId($ridePaymentId);
        }

        return self::mapRecordToObject($record);
    }

    public function getByRideId(string $rideId): RidePayment
    {
        $record = $this->connection->fetchAssociative(
            'SELECT * FROM billing.projection_ride_payment WHERE ride_id = :ride_id',
            ['ride_id' => $rideId],
        );

        if (false === $record) {
            throw RidePaymentNotFound::forRideId($rideId);
        }

        return self::mapRecordToObject($record);
    }

    public function list(): array
    {
        return \array_map(
            self::mapRecordToObject(...),
            $this->connection->fetchAllAssociative('SELECT * FROM billing.projection_ride_payment'),
        );
    }

    /**
     * @param array{
     *    ride_payment_id: string,
     *    ride_id: string,
     *    total_price: string,
     *    price_per_minute: string,
     *    initiated_at: string,
     *    captured_at: ?string,
     *    external_payment_ref: string,
     *  } $record
     */
    private static function mapRecordToObject(array $record): RidePayment
    {
        return new RidePayment(
            $record['ride_payment_id'],
            $record['ride_id'],
            Money::fromArray(Json::decode($record['total_price'])),
            Money::fromArray(Json::decode($record['price_per_minute'])),
            Timestamp::from($record['initiated_at']),
            Timestamp::fromNullable($record['captured_at']),
            $record['external_payment_ref'],
        );
    }

    /**
     * @return array{
     *   ride_payment_id: string,
     *   ride_id: string,
     *   total_price: string,
     *   price_per_minute: string,
     *   initiated_at: string,
     *   captured_at: ?string,
     *   external_payment_ref: string,
     * }
     */
    private static function mapObjectToRecord(RidePayment $ridePayment): array
    {
        return [
            'ride_payment_id' => $ridePayment->ridePaymentId,
            'ride_id' => $ridePayment->rideId,
            'total_price' => Json::encode($ridePayment->totalPrice->toArray()),
            'price_per_minute' => Json::encode($ridePayment->pricePerMinute->toArray()),
            'initiated_at' => Timestamp::format($ridePayment->initiatedAt),
            'captured_at' => Timestamp::formatNullable($ridePayment->capturedAt),
            'external_payment_ref' => $ridePayment->externalRef,
        ];
    }
}
