<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http\CreateRider;

use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rides/rider', name: 'rides:rider:create', methods: ['POST'])]
final class CreateRiderController
{
    public function __invoke(CommandBus $bus, CreateRiderInput $input): Response
    {
        $bus->dispatch(new CreateRiderCommand($input->riderId));

        return new Response();
    }
}
