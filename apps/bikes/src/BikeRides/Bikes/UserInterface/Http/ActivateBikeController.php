<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http;

use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bike/{bikeId}/activate', name: 'bike:activate', methods: ['POST'])]
final class ActivateBikeController
{
    public function __invoke(CommandBus $bus, string $bikeId): Response
    {
        $bus->dispatch(new ActivateBikeCommand($bikeId));

        return new Response(status: Response::HTTP_OK);
    }
}
