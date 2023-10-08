<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Ui;

use App\BikeRides\Shared\Domain\Event\RideEnded;
use App\Tests\BikeRides\Shared\Ui\UiTestCase;
use Money\Money;
use Monolog\Handler\TestHandler;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RidePaymentTest extends UiTestCase
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

        $this->handleEvent(new RideEnded($rideId));

        $ridePayments = $this->fetchRidePayments($rideId);

        self::assertSame(1, $ridePayments['total']);
        self::assertCount(1, $ridePayments['_embedded']['ride-payment']);

        $ridePayment = $ridePayments['_embedded']['ride-payment'][0];

        self::assertSame($rideId, $ridePayment['ride_id']);
        self::assertSame(Money::GBP(15_00)->jsonSerialize(), $ridePayment['total_price']);
        self::assertSame(Money::GBP(25)->jsonSerialize(), $ridePayment['price_per_minute']);
        self::assertIsNumeric($ridePayment['initiated_at']);
    }

    public function test_ride_payment_deduplication(): void
    {
        self::getContainer()->get('logger')->pushHandler($logHandler = new TestHandler());
        $logMessage = 'Duplicate ride payment';

        $rideId = 'ride_id';

        $fetchRideDetailsHttpResponse = new JsonMockResponse(
            [
                'started_at' => (new \DateTimeImmutable('-2 hour'))->getTimestamp(),
                'ended_at' => (new \DateTimeImmutable('-1 hour'))->getTimestamp(),
            ],
        );

        self::getContainer()->set(HttpClientInterface::class, new MockHttpClient($fetchRideDetailsHttpResponse));

        $this->handleEvent(new RideEnded($rideId));

        self::assertSame(1, $this->fetchRidePayments($rideId)['total']);

        self::assertFalse($logHandler->hasNotice($logMessage));

        $this->handleEvent(new RideEnded($rideId));

        self::assertSame(1, $this->fetchRidePayments($rideId)['total']);

        self::assertTrue($logHandler->hasNotice($logMessage));
    }

    private function fetchRidePayments(string $rideId): array
    {
        return $this->getJson('/billing/ride-payment?ride-id=' . $rideId);
    }
}
