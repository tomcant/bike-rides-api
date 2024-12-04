<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http;

use App\BikeRides\Rides\Application\Command\EndRide\EndRideCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rides/ride/{rideId}/end', name: 'rides:ride:end', methods: ['POST'])]
final class EndRideController
{
    public function __invoke(CommandBus $bus, string $rideId): Response
    {
        $bus->dispatch(new EndRideCommand($rideId));

        return new Response();
    }
}
