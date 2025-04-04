<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http\CreateRider;

use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rider', name: 'rider:create', methods: ['POST'])]
final class CreateRiderController
{
    public function __invoke(CommandBus $bus, CreateRiderInput $input): Response
    {
        $bus->dispatch(new CreateRiderCommand($input->riderId));

        return new Response(status: Response::HTTP_OK);
    }
}
