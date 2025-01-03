<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Infrastructure\LoggingRidePaymentGateway;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

final class LoggingRidePaymentGatewayTest extends TestCase
{
    public function test_it_logs_when_a_payment_is_captured(): void
    {
        $testHandler = new TestHandler();
        $paymentGateway = new LoggingRidePaymentGateway(new Logger('test', [$testHandler]));

        $paymentGateway->capture(RidePaymentId::generate());

        self::assertTrue($testHandler->hasInfoThatContains('Ride payment captured'));
    }
}
