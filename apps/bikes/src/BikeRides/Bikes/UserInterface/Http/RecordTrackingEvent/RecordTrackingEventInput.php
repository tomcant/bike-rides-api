<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http\RecordTrackingEvent;

use App\Framework\JsonSchemaInput\JsonSchemaInput;
use BikeRides\SharedKernel\Domain\Model\Location;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class RecordTrackingEventInput implements JsonSchemaInput
{
    private function __construct(
        public int $bikeId,
        public Location $location,
    ) {
    }

    public static function fromPayload(array $payload): JsonSchemaInput
    {
        try {
            $location = Location::fromArray($payload['location']);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException();
        }

        return new self($payload['bike_id'], $location);
    }

    public static function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'bike_id' => [
                    'type' => 'integer',
                ],
                'location' => [
                    'type' => 'object',
                    'properties' => [
                        'latitude' => [
                            'type' => 'number',
                        ],
                        'longitude' => [
                            'type' => 'number',
                        ],
                    ],
                    'required' => [
                        'latitude',
                        'longitude',
                    ],
                ],
            ],
            'required' => [
                'bike_id',
                'location',
            ],
        ];
    }
}
