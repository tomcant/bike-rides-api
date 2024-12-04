<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http\LocateBike;

use App\BikeRides\Bikes\Application\Command\LocateBike\LocateBikeCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\Foundation\Clock\Clock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bikes/bike/locate', name: 'bikes:bike:locate', methods: ['POST'])]
final class LocateBikeController
{
    public function __invoke(CommandBus $bus, LocateBikeInput $input): Response
    {
        $bus->dispatch(new LocateBikeCommand($input->bikeId, $input->location, Clock::now()));

        return new Response(status: Response::HTTP_OK);
    }
}
