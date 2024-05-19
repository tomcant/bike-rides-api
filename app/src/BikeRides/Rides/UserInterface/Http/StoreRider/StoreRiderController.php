<?php declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Http\StoreRider;

use App\BikeRides\Rides\Application\Command\StoreRider\StoreRiderCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rides/rider', name: 'rides:rider:store', methods: ['POST'])]
final class StoreRiderController
{
    public function __invoke(CommandBus $bus, StoreRiderInput $input): Response
    {
        $bus->dispatch(new StoreRiderCommand($input->riderId));

        return new Response();
    }
}
