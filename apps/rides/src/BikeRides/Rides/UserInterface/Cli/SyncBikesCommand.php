<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Cli;

use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\SharedKernel\Domain\Model\Location;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'rides:sync-bikes')]
final class SyncBikesCommand extends Command
{
    public function __construct(
        private readonly CommandBus $bus,
        private readonly Connection $connection,
        private readonly HttpClientInterface $httpClient,
        private readonly string $listBikesApiUrl,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $bikes = $this->httpClient->request('GET', $this->listBikesApiUrl)->toArray();

        $this->connection->beginTransaction();
        $this->connection->executeStatement('TRUNCATE rides.bikes');

        foreach ($bikes['_embedded']['bike'] as $bike) {
            if (!$bike['is_active']) {
                continue;
            }

            $this->bus->dispatch(
                new CreateBikeCommand(
                    $bike['bike_id'],
                    Location::fromArray($bike['location']),
                ),
            );

            $output->writeln("Synced bike ID <info>{$bike['bike_id']}</info>");
        }

        $this->connection->commit();

        return Command::SUCCESS;
    }
}
