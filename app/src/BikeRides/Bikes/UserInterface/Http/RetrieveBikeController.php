<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Query\GetBikeById;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/bikes/bike/{bikeId}', name: 'bikes:bike:retrieve', methods: ['GET'])]
final class RetrieveBikeController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetBikeById $query,
        string $bikeId,
    ): JsonResponse {
        $bike = $query->query($bikeId);

        if (null === $bike) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse([
            '_links' => \array_filter([
                'self' => [
                    'href' => $urlGenerator->generate(
                        'bikes:bike:retrieve',
                        ['bikeId' => $bike['bike_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
                'activate' => $bike['is_active'] ? null : [
                    'href' => $urlGenerator->generate(
                        'bikes:bike:activate',
                        ['bikeId' => $bike['bike_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'POST',
                ],
            ]),
            'bike_id' => $bike['bike_id'],
            'location' => $bike['location'],
            'is_active' => $bike['is_active'],
        ]);
    }
}
