<?php

declare(strict_types=1);

namespace BikeRides\Foundation;

final class Timestamp
{
    public static function from(string $dateTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable($dateTime);
    }

    public static function fromNullable(?string $dateTime): ?\DateTimeImmutable
    {
        return $dateTime ? new \DateTimeImmutable($dateTime) : null;
    }

    public static function format(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s.u O');
    }

    public static function formatNullable(?\DateTimeInterface $dateTime): ?string
    {
        return $dateTime?->format('Y-m-d H:i:s.u O');
    }
}
