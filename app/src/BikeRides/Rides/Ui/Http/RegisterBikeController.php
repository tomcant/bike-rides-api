<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Ui\Http;

use App\BikeRides\Rides\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/rides/bike', name: 'rides:bike:register', methods: ['POST'])]
final class RegisterBikeController
{
    public function __invoke(CommandBus $bus, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $bikeId = Uuid::v4()->toRfc4122();
        $bus->dispatch(new RegisterBikeCommand($bikeId));

        return new JsonResponse(
            ['bike_id' => $bikeId],
            Response::HTTP_CREATED,
            [
                'Link' => \sprintf(
                    '<%s>; rel="bike"',
                    $urlGenerator->generate(
                        'rides:bike:retrieve',
                        ['bikeId' => $bikeId],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
            ],
        );
    }
}
