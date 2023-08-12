<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Ui\Http\StartRide;

use App\BikeRides\Rides\Application\Command\StartRide\StartRideCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/rides/bike/{bikeId}/start-ride', name: 'rides:bike:start-ride', methods: ['POST'])]
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
        } catch (\DomainException $exception) {
            return new JsonResponse(
                ['error' => $exception->getMessage()],
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse(
            ['ride_id' => $rideId],
            Response::HTTP_CREATED,
            [
                'Link' => \sprintf(
                    '<%s>; rel="ride"',
                    $urlGenerator->generate(
                        'rides:ride:retrieve',
                        ['rideId' => $rideId],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
            ],
        );
    }
}
