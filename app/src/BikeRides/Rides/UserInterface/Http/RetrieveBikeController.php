<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http;

use App\BikeRides\Rides\Application\Query\GetBikeById;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/rides/bike/{bikeId}', name: 'rides:bike:retrieve', methods: ['GET'])]
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
        ]);
    }
}
