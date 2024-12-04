<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http\CreateRider;

use App\Framework\JsonSchemaInput\JsonSchemaInput;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class CreateRiderInput implements JsonSchemaInput
{
    private function __construct(public string $riderId)
    {
    }

    public static function fromPayload(array $payload): JsonSchemaInput
    {
        $riderId = \mb_trim($payload['rider_id']);

        if ('' === $riderId) {
            throw new BadRequestHttpException();
        }

        return new self($riderId);
    }

    public static function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'rider_id' => [
                    'type' => 'string',
                ],
            ],
            'required' => [
                'rider_id',
            ],
        ];
    }
}
