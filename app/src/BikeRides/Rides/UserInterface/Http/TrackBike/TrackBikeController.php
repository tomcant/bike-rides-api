<?php declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http\TrackBike;

use App\BikeRides\Rides\Application\Command\TrackBike\TrackBikeCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\Foundation\Clock\Clock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bike/track', name: 'bike:track', methods: ['POST'])]
final class TrackBikeController
{
    public function __invoke(CommandBus $bus, TrackBikeInput $input): Response
    {
        $bus->dispatch(new TrackBikeCommand($input->bikeId, $input->location, Clock::now()));

        return new Response(status: Response::HTTP_OK);
    }
}
