<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Functional\UserInterface;

use BikeRides\SharedKernel\Domain\Event\RideEnded;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RidePaymentListingTest extends UserInterfaceTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::getContainer()->set(
            HttpClientInterface::class,
            new MockHttpClient(
                static fn () => new JsonMockResponse([
                    'started_at' => (new \DateTimeImmutable('-2 hour'))->getTimestamp(),
                    'ended_at' => (new \DateTimeImmutable('-1 hour'))->getTimestamp(),
                ]),
            ),
        );
    }

    public function test_it_lists_ride_payments(): void
    {
        $this->initiateRidePayment($rideId1 = RideId::generate()->toString());
        $this->initiateRidePayment($rideId2 = RideId::generate()->toString());

        $list = $this->getJson('/ride-payment');

        self::assertSame(2, $list['total']);
        self::assertCount(2, $list['_embedded']['ride-payment']);
        self::assertSame($rideId1, $list['_embedded']['ride-payment'][0]['ride_id']);
        self::assertSame($rideId2, $list['_embedded']['ride-payment'][1]['ride_id']);
    }

    public function test_it_lists_ride_payments_filtered_by_ride_id(): void
    {
        $this->initiateRidePayment($rideId = RideId::generate()->toString());
        $this->initiateRidePayment(RideId::generate()->toString());

        $list = $this->getJson("/ride-payment?ride_id={$rideId}");

        self::assertSame(1, $list['total']);
        self::assertCount(1, $list['_embedded']['ride-payment']);
        self::assertSame($rideId, $list['_embedded']['ride-payment'][0]['ride_id']);
    }

    protected function initiateRidePayment(string $rideId): void
    {
        $this->handleDomainEvent(new RideEnded($rideId, 'bike_id'));
    }
}
