<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Query\GetBikeById;
use App\BikeRides\Bikes\Application\Query\ListBikeLocationsByBikeId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/bikes/bike/{bikeId}/locations', name: 'bikes:bike-location:list', methods: ['GET'])]
final class ListBikeLocationsController
{
    public function __invoke(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        GetBikeById $getBike,
        ListBikeLocationsByBikeId $listBikeLocations,
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
            throw new NotFoundHttpException();
        }

        $from = (new \DateTimeImmutable())->setTimestamp($from);
        $to = (new \DateTimeImmutable())->setTimestamp($to);

        $embeddedBikeLocations = \array_map(
            static fn (array $bikeLocation) => [
                'location' => $bikeLocation['location'],
                'locatedAt' => $bikeLocation['locatedAt']->getTimestamp(),
            ],
            $listBikeLocations->query($from, $to, $bikeId),
        );

        return new JsonResponse([
            '_links' => \array_filter([
                'self' => [
                    'href' => $urlGenerator->generate(
                        'bikes:bike-location:list',
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
            ]),
            '_embedded' => [
                'bike_location' => $embeddedBikeLocations,
            ],
            'total' => \count($embeddedBikeLocations),
        ]);
    }
}
