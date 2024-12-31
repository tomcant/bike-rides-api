<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http;

use App\BikeRides\Rides\Application\Query\GetRideSummaryByRideId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/ride/{rideId}/summary', name: 'ride:summary', methods: ['GET'])]
final class RetrieveRideSummaryController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetRideSummaryByRideId $query,
        string $rideId,
    ): JsonResponse {
        $summary = $query->query($rideId);

        if (null === $summary) {
            throw new NotFoundHttpException("Could not find ride summary for ride with ID {$rideId}");
        }

        return new JsonResponse([
            '_links' => [
                'self' => [
                    'href' => $urlGenerator->generate(
                        'ride:summary',
                        ['rideId' => $summary['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
                'ride' => [
                    'href' => $urlGenerator->generate(
                        'ride:retrieve',
                        ['rideId' => $summary['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
            ],
            'ride_id' => $summary['ride_id'],
            'duration' => [
                'started_at' => $summary['duration']['started_at']->getTimestamp(),
                'ended_at' => $summary['duration']['ended_at']->getTimestamp(),
                'minutes' => $summary['duration']['minutes'],
            ],
            'route' => $summary['route'],
        ]);
    }
}
