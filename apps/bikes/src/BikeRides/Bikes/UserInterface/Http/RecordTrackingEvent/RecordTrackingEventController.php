<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http\RecordTrackingEvent;

use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Clock\Clock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tracking', name: 'tracking:record', methods: ['POST'])]
final class RecordTrackingEventController
{
    public function __invoke(CommandBus $bus, RecordTrackingEventInput $input): Response
    {
        $bus->dispatch(new RecordTrackingEventCommand($input->bikeId, $input->location, Clock::now()));

        return new Response(status: Response::HTTP_OK);
    }
}
