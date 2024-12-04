<?php

declare(strict_types=1);

namespace App\Framework\JsonSchemaInput;

interface JsonSchemaInput
{
    public static function getSchema(): array;

    public static function fromPayload(array $payload): self;
}
