<?php

declare(strict_types=1);

namespace BikeRides\Foundation;

final class Json
{
    public static function encode(mixed $value, int $options = \JSON_THROW_ON_ERROR, int $depth = 512): string
    {
        return \json_encode($value, $options, $depth);
    }

    /** @return array<mixed, mixed> */
    public static function decode(string $json, bool $associative = true, int $depth = 512, int $options = \JSON_THROW_ON_ERROR): array
    {
        return \json_decode($json, $associative, $depth, $options) ?? [];
    }
}
