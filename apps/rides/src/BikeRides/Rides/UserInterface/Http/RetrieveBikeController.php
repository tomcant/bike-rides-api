<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http;

use App\BikeRides\Rides\Application\Query\GetBikeById;
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
            throw new NotFoundHttpException("Could not find bike with ID '{$bikeId}'");
        }

        return new JsonResponse([
            '_links' => [
                'self' => [
                    'href' => $urlGenerator->generate(
                        'bike:retrieve',
                        ['bikeId' => $bike['bike_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
                'start-ride' => [
                    'href' => $urlGenerator->generate(
                        'bike:start-ride',
                        ['bikeId' => $bike['bike_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'POST',
                ],
            ],
            'bike_id' => $bike['bike_id'],
            'location' => $bike['location'],
        ]);
    }
}
