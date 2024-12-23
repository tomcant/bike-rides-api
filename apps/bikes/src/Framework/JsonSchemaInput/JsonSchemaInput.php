<?php

declare(strict_types=1);

namespace App\Framework\JsonSchemaInput;

interface JsonSchemaInput
{
    /** @return array<mixed, mixed> */
    public static function getSchema(): array;

    /** @param array<mixed, mixed> $payload */
    public static function fromPayload(array $payload): self;
}
