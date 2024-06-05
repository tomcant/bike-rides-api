<?php declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http\ActivateBike;

use App\Foundation\Location;
use App\Framework\JsonSchemaInput\JsonSchemaInput;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class ActivateBikeInput implements JsonSchemaInput
{
    private function __construct(public Location $location)
    {
    }

    public static function fromPayload(array $payload): JsonSchemaInput
    {
        try {
            $location = Location::fromArray($payload['location']);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException();
        }

        return new self($location);
    }

    public static function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
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
                'location',
            ],
        ];
    }
}
