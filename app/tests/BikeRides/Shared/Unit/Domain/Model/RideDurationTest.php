<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Unit\Domain\Model;

use App\BikeRides\Shared\Domain\Model\RideDuration;
use PHPUnit\Framework\TestCase;

final class RideDurationTest extends TestCase
{
    public function test_it_calculates_the_ride_duration_in_minutes(): void
    {
        $startedAt = (new \DateTimeImmutable())->setTime(hour: 12, minute: 30);
        $endedAt = (new \DateTimeImmutable())->setTime(hour: 12, minute: 40);

        $rideDuration = RideDuration::fromStartAndEnd($startedAt, $endedAt);

        self::assertSame(10, $rideDuration->minutes);
    }

    public function test_the_minimum_ride_duration_is_one_minute(): void
    {
        $startedAt = (new \DateTimeImmutable())->setTime(hour: 12, minute: 30);
        $endedAt = (new \DateTimeImmutable())->setTime(hour: 12, minute: 30, second: 1);

        $rideDuration = RideDuration::fromStartAndEnd($startedAt, $endedAt);

        self::assertSame(1, $rideDuration->minutes);
    }

    public function test_the_start_date_cannot_be_after_the_end_date(): void
    {
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Ride end date/time must be after start date/time');

        RideDuration::fromStartAndEnd(
            startedAt: new \DateTimeImmutable('+1 second'),
            endedAt: new \DateTimeImmutable('now'),
        );
    }

    public function test_the_start_date_cannot_match_the_end_date(): void
    {
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Ride end date/time must be after start date/time');

        RideDuration::fromStartAndEnd(
            $startedAt = new \DateTimeImmutable('now'),
            endedAt: $startedAt,
        );
    }
}
