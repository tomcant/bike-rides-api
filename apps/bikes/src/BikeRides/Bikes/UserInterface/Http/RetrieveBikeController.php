<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Query\GetBikeById;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/bike/{bikeId}', name: 'bike:retrieve', methods: ['GET'])]
final class RetrieveBikeController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetBikeById $query,
        int $bikeId,
    ): JsonResponse {
        $bike = $query->query($bikeId);

        if (null === $bike) {
            throw new NotFoundHttpException("Could not find bike with ID {$bikeId}");
        }

        return new JsonResponse([
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
                'deactivate' => !$bike['is_active'] ? null : [
                    'href' => $urlGenerator->generate(
                        'bike:deactivate',
                        ['bikeId' => $bike['bike_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'POST',
                ],
            ]),
            'bike_id' => $bike['bike_id'],
            'is_active' => $bike['is_active'],
            'location' => $bike['location'],
        ]);
    }
}
