<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http\StartRide;

use App\BikeRides\Rides\Application\Command\StartRide\StartRideCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/bike/{bikeId}/start-ride', name: 'bike:start-ride', methods: ['POST'])]
final class StartRideController
{
    public function __invoke(
        CommandBus $bus,
        UrlGeneratorInterface $urlGenerator,
        StartRideInput $input,
        string $bikeId,
    ): JsonResponse {
        $rideId = Uuid::v4()->toRfc4122();

        try {
            $bus->dispatch(new StartRideCommand($rideId, $input->riderId, $bikeId));
        } catch (\RuntimeException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        return new JsonResponse(
            ['ride_id' => $rideId],
            Response::HTTP_CREATED,
            [
                'Link' => \sprintf(
                    '<%s>; rel="ride"',
                    $urlGenerator->generate(
                        'ride:retrieve',
                        ['rideId' => $rideId],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
            ],
        );
    }
}
