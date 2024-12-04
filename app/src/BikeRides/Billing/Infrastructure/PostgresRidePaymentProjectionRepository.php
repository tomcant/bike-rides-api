<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentNotFound;
use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePaymentProjectionRepository;
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

    private static function mapRecordToObject(array $record): RidePayment
    {
        return new RidePayment(
            $record['ride_payment_id'],
            $record['ride_id'],
            \money_from_array(\json_decode_array($record['total_price'])),
            \money_from_array(\json_decode_array($record['price_per_minute'])),
            new \DateTimeImmutable($record['initiated_at']),
            $record['captured_at'] ? new \DateTimeImmutable($record['captured_at']) : null,
            $record['external_payment_ref'],
        );
    }

    private static function mapObjectToRecord(RidePayment $ridePayment): array
    {
        return [
            'ride_payment_id' => $ridePayment->ridePaymentId,
            'ride_id' => $ridePayment->rideId,
            'total_price' => \json_encode_array($ridePayment->totalPrice->jsonSerialize()),
            'price_per_minute' => \json_encode_array($ridePayment->pricePerMinute->jsonSerialize()),
            'initiated_at' => \datetime_timestamp($ridePayment->initiatedAt),
            'captured_at' => \datetime_optional_timestamp($ridePayment->capturedAt),
            'external_payment_ref' => $ridePayment->externalRef,
        ];
    }
}
