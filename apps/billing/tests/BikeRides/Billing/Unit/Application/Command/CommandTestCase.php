<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Unit\Application\Command;

use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Clock\ClockStub;
use BikeRides\Foundation\Domain\DomainEvent;
use PHPUnit\Framework\TestCase;

abstract class CommandTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Clock::useClock(new ClockStub());
    }

    protected static function assertDomainEventEquals(DomainEvent $expected, DomainEvent $actual): void
    {
        self::assertEquals($expected->serialize(), $actual->serialize());
    }
}
