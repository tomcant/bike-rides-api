<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Ui\Http;

use App\BikeRides\Rides\Application\Query\GetRideSummaryByRideId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/rides/ride/{rideId}/summary', name: 'rides:ride:summary', methods: ['GET'])]
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
            throw new NotFoundHttpException();
        }

        return new JsonResponse([
            '_links' => \array_filter([
                'self' => [
                    'href' => $urlGenerator->generate(
                        'rides:ride:summary',
                        ['rideId' => $summary['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
                'ride' => [
                    'href' => $urlGenerator->generate(
                        'rides:ride:retrieve',
                        ['rideId' => $summary['ride_id']],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    'method' => 'GET',
                ],
            ]),
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
