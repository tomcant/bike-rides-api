<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Query\ListBikes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/bike', name: 'bike:list', methods: ['GET'])]
final class ListBikesController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        ListBikes $listBikes,
    ): JsonResponse {
        $embeddedBikes = \array_map(
            static fn (array $bike) => [
                '_links' => \array_filter([
                    'self' => [
                        'href' => $urlGenerator->generate(
                            'bike:retrieve',
                            ['bikeId' => $bike['bike_id']],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        ),
                        'method' => 'GET',
                    ],
                    'activate' => $bike['is_active'] ? null : [
                        'href' => $urlGenerator->generate(
                            'bike:activate',
                            ['bikeId' => $bike['bike_id']],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        ),
                        'method' => 'POST',
                    ],
                ]),
                'bike_id' => $bike['bike_id'],
                'is_active' => $bike['is_active'],
            ],
            $listBikes->query(),
        );

        return new JsonResponse(
            [
                '_links' => [
                    'self' => [
                        'href' => $urlGenerator->generate('bike:list'),
                        'method' => 'GET',
                    ],
                    'bike' => \array_map(
                        static fn (array $bike) => $bike['_links']['self'],
                        $embeddedBikes,
                    ),
                ],
                '_embedded' => [
                    'bike' => $embeddedBikes,
                ],
                'total' => \count($embeddedBikes),
            ],
            headers: ['Content-Type' => 'application/hal+json'],
        );
    }
}
