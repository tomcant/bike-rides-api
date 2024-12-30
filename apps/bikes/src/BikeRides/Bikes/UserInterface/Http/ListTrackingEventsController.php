<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Query\GetBikeById;
use App\BikeRides\Bikes\Application\Query\ListTrackingEventsByBikeId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/bikes/tracking', name: 'bikes:tracking:list', methods: ['GET'])]
final class ListTrackingEventsController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetBikeById $getBike,
        ListTrackingEventsByBikeId $listTrackingEvents,
        #[MapQueryParameter(name: 'bike_id')]
        string $bikeId,
        #[MapQueryParameter(
            filter: \FILTER_VALIDATE_INT,
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST,
        )]
        int $from,
        #[MapQueryParameter(
            filter: \FILTER_VALIDATE_INT,
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST,
        )]
        int $to,
    ): JsonResponse {
        $bike = $getBike->query($bikeId);

        if (null === $bike) {
            throw new NotFoundHttpException("Could not find bike with ID {$bikeId}");
        }

        $from = (new \DateTimeImmutable())->setTimestamp($from);
        $to = (new \DateTimeImmutable())->setTimestamp($to);

        $embeddedTrackingEvents = \array_map(
            static fn (array $event) => [
                'location' => $event['location'],
                'tracked_at' => $event['trackedAt']->getTimestamp(),
            ],
            $listTrackingEvents->query($bikeId, $from, $to),
        );

        return new JsonResponse(
            [
                '_links' => [
                    'self' => [
                        'href' => $urlGenerator->generate(
                            'bikes:tracking:list',
                            ['bikeId' => $bike['bike_id']],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        ),
                        'method' => 'GET',
                    ],
                    'bike' => [
                        'href' => $urlGenerator->generate(
                            'bikes:bike:retrieve',
                            ['bikeId' => $bike['bike_id']],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        ),
                        'method' => 'GET',
                    ],
                ],
                '_embedded' => [
                    'tracking_event' => $embeddedTrackingEvents,
                ],
                'total' => \count($embeddedTrackingEvents),
            ],
            headers: ['Content-Type' => 'application/hal+json'],
        );
    }
}
