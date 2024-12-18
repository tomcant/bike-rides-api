<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Functional\UserInterface;

use App\BikeRides\Shared\Domain\Event\RideEnded;
use App\Tests\BikeRides\Shared\Functional\UserInterface\UserInterfaceTestCase;
use Money\Money;
use Monolog\Handler\TestHandler;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RidePaymentTest extends UserInterfaceTestCase
{
    public function test_ride_payment_initiation(): void
    {
        $rideId = 'ride_id';

        $fetchRideDetailsHttpResponse = new JsonMockResponse(
            [
                'started_at' => (new \DateTimeImmutable('-2 hour'))->getTimestamp(),
                'ended_at' => (new \DateTimeImmutable('-1 hour'))->getTimestamp(),
            ],
        );

        self::getContainer()->set(HttpClientInterface::class, new MockHttpClient($fetchRideDetailsHttpResponse));

        $this->publishEvent(new RideEnded($rideId, 'bike_id'));

        $ridePayments = $this->fetchRidePayments($rideId);

        self::assertSame(1, $ridePayments['total']);
        self::assertCount(1, $ridePayments['_embedded']['ride-payment']);

        $ridePayment = $ridePayments['_embedded']['ride-payment'][0];

        self::assertSame($rideId, $ridePayment['ride_id']);
        self::assertSame(Money::GBP(15_00)->jsonSerialize(), $ridePayment['total_price']);
        self::assertSame(Money::GBP(25)->jsonSerialize(), $ridePayment['price_per_minute']);

        self::assertIsNumeric($ridePayment['initiated_at']);
        self::assertGreaterThan($ridePayment['initiated_at'], $ridePayment['captured_at']);
        self::assertSame('external_payment_ref', $ridePayment['external_payment_ref']);
    }

    public function test_ride_payment_deduplication(): void
    {
        self::getContainer()->get('logger')->pushHandler($logHandler = new TestHandler());
        $expectedLogMessage = 'Duplicate ride payment';

        $rideId = 'ride_id';

        $fetchRideDetailsHttpResponse = new JsonMockResponse(
            [
                'started_at' => (new \DateTimeImmutable('-2 hour'))->getTimestamp(),
                'ended_at' => (new \DateTimeImmutable('-1 hour'))->getTimestamp(),
            ],
        );

        self::getContainer()->set(HttpClientInterface::class, new MockHttpClient($fetchRideDetailsHttpResponse));

        $this->publishEvent(new RideEnded($rideId, 'bike_id'));

        self::assertSame(1, $this->fetchRidePayments($rideId)['total']);
        self::assertFalse($logHandler->hasNotice($expectedLogMessage));

        $this->publishEvent(new RideEnded($rideId, 'bike_id'));

        self::assertSame(1, $this->fetchRidePayments($rideId)['total']);
        self::assertTrue($logHandler->hasNotice($expectedLogMessage));
    }

    private function fetchRidePayments(string $rideId): array
    {
        return $this->getJson("/billing/ride-payment?rideId={$rideId}");
    }
}
