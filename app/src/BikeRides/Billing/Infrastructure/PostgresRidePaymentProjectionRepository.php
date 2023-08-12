<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Domain\Projection\RidePayment\RidePayment;
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
                    (ride_payment_id, ride_id, total_price, price_per_minute, initiated_at)
                VALUES
                    (:ride_payment_id, :ride_id, :total_price, :price_per_minute, :initiated_at)
                ON CONFLICT (ride_payment_id) DO UPDATE
                    SET ride_id = :ride_id,
                        total_price = :total_price,
                        price_per_minute = :price_per_minute,
                        initiated_at = :initiated_at
            ',
            self::mapObjectToRecord($ridePayment),
        );
    }

    public function listByRideId(string $rideId): array
    {
        $records = $this->connection->fetchAllAssociative(
            'SELECT * FROM billing.projection_ride_payment WHERE ride_id = :ride_id',
            ['ride_id' => $rideId],
        );

        return \array_map(self::mapRecordToObject(...), $records);
    }

    private static function mapRecordToObject(array $record): RidePayment
    {
        return new RidePayment(
            $record['ride_payment_id'],
            $record['ride_id'],
            \money_from_array(\json_decode_array($record['total_price'])),
            \money_from_array(\json_decode_array($record['price_per_minute'])),
            new \DateTimeImmutable($record['initiated_at']),
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
        ];
    }
}
