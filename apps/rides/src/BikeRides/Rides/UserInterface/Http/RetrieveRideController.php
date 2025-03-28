<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http;

use App\BikeRides\Rides\Application\Query\GetRideById;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/ride/{rideId}', name: 'ride:retrieve', methods: ['GET'])]
final class RetrieveRideController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetRideById $query,
        string $rideId,
    ): JsonResponse {
        $ride = $query->query($rideId);

        if (null === $ride) {
            throw new NotFoundHttpException("Could not find ride with ID {$rideId}");
        }

        return new JsonResponse([
            '_links' => \array_filter([
                'self' => [
                    'href' => $urlGenerator->generate(
                        'ride:retrieve',
                        ['rideId' => $ride['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
                'end' => null !== $ride['ended_at'] ? null : [
                    'href' => $urlGenerator->generate(
                        'ride:end',
                        ['rideId' => $ride['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'POST',
                ],
                'summary' => null === $ride['ended_at'] ? null : [
                    'href' => $urlGenerator->generate(
                        'ride:summary',
                        ['rideId' => $ride['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
            ]),
            'ride_id' => $ride['ride_id'],
            'rider_id' => $ride['rider_id'],
            'bike_id' => $ride['bike_id'],
            'started_at' => $ride['started_at']->getTimestamp(),
            'ended_at' => $ride['ended_at']?->getTimestamp(),
        ]);
    }
}
