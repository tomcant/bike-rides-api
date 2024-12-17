<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http\ActivateBike;

use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bikes/bike/{bikeId}/activate', name: 'bikes:bike:activate', methods: ['POST'])]
final class ActivateBikeController
{
    public function __invoke(CommandBus $bus, ActivateBikeInput $input, string $bikeId): Response
    {
        $bus->dispatch(new ActivateBikeCommand($bikeId, $input->location));

        return new Response(status: Response::HTTP_OK);
    }
}
