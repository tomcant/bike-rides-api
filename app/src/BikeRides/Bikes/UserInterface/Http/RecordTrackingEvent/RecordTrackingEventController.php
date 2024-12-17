<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Http\RecordTrackingEvent;

use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\Foundation\Clock\Clock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bikes/tracking', name: 'bikes:tracking:record', methods: ['POST'])]
final class RecordTrackingEventController
{
    public function __invoke(CommandBus $bus, RecordTrackingEventInput $input): Response
    {
        $bus->dispatch(new RecordTrackingEventCommand($input->bikeId, $input->location, Clock::now()));

        return new Response(status: Response::HTTP_OK);
    }
}
