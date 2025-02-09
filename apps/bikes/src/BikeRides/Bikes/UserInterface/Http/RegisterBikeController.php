<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Bikes\Application\Query\GetBikeIdByRegistrationCorrelationId;
use BikeRides\Foundation\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/bike', name: 'bike:register', methods: ['POST'])]
final class RegisterBikeController
{
    public function __invoke(
        CommandBus $bus,
        GetBikeIdByRegistrationCorrelationId $getBikeIdByRegistrationCorrelationId,
        UrlGeneratorInterface $urlGenerator,
    ): JsonResponse {
        $correlationId = Uuid::v4()->toRfc4122();
        $bus->dispatch(new RegisterBikeCommand($correlationId));
        $bikeId = $getBikeIdByRegistrationCorrelationId->query($correlationId);

        return new JsonResponse(
            ['bike_id' => $bikeId],
            Response::HTTP_CREATED,
            [
                'Link' => \sprintf(
                    '<%s>; rel="bike"',
                    $urlGenerator->generate(
                        'bike:retrieve',
                        ['bikeId' => $bikeId],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
            ],
        );
    }
}
