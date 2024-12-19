<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Cli\Fixture;

use App\BikeRides\Shared\UserInterface\Cli\FixtureCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'bikes:fixture:bike')]
final class BikeFixtureCommand extends FixtureCommand
{
    private const float DEFAULT_LATITUDE = 51.535704;
    private const float DEFAULT_LONGITUDE = -0.126946;

    public function doExecute(InputInterface $input, OutputInterface $output): int
    {
        ['bike_id' => $bikeId] = $this->postJson('/bikes/bike');

        $bike = $this->getJson($this->parseResponseLinkUrl());

        $this->postJson(
            '/bikes/tracking',
            [
                'bike_id' => $bikeId,
                'location' => [
                    'latitude' => (float) $input->getOption('latitude'),
                    'longitude' => (float) $input->getOption('longitude'),
                ],
            ],
        );

        $this->postJson($bike['_links']['activate']['href']);

        $output->writeln(\sprintf("\nBike ID: <info>%s</info>\n", $bikeId));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('latitude', mode: InputOption::VALUE_OPTIONAL, default: self::DEFAULT_LATITUDE);
        $this->addOption('longitude', mode: InputOption::VALUE_OPTIONAL, default: self::DEFAULT_LONGITUDE);
    }
}
