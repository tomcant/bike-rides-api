<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Command\DeactivateBike\DeactivateBikeCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bike/{bikeId}/deactivate', name: 'bike:deactivate', methods: ['POST'])]
final class DeactivateBikeController
{
    public function __invoke(CommandBus $bus, int $bikeId): Response
    {
        $bus->dispatch(new DeactivateBikeCommand($bikeId));

        return new Response(status: Response::HTTP_OK);
    }
}
