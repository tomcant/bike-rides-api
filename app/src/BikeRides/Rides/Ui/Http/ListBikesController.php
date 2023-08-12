<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Ui\Http;

use App\BikeRides\Rides\Application\Query\ListBikes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/rides/bike', name: 'rides:bike:list', methods: ['GET'])]
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
                            'rides:bike:retrieve',
                            ['bikeId' => $bike['bike_id']],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        ),
                        'method' => 'GET',
                    ],
                    'start-ride' => [
                        'href' => $urlGenerator->generate(
                            'rides:bike:start-ride',
                            ['bikeId' => $bike['bike_id']],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        ),
                        'method' => 'POST',
                    ],
                ]),
                'bike_id' => $bike['bike_id'],
                'location' => $bike['location'],
            ],
            $listBikes->query(),
        );

        return new JsonResponse(
            [
                '_links' => [
                    'self' => [
                        'href' => $urlGenerator->generate('rides:bike:list'),
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
