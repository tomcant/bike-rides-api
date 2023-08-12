<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Ui\Cli\Fixture;

use App\BikeRides\Shared\Ui\Cli\FixtureCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fixture:bike')]
final class BikeFixtureCommand extends FixtureCommand
{
    private const DEFAULT_LATITUDE = 51.535704;
    private const DEFAULT_LONGITUDE = -0.126946;

    public function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->postJson('/rides/rider', ['rider_id' => 'rider_id']);

        $this->postJson('/rides/bike');

        $bike = $this->getJson($this->parseResponseLinkUrl());

        $this->postJson('/bike/track', [
            'bike_id' => $bike['bike_id'],
            'location' => [
                'latitude' => (float) $input->getOption('latitude'),
                'longitude' => (float) $input->getOption('longitude'),
            ],
        ]);

        $output->writeln(\sprintf("\nBike ID: <info>%s</info>\n", $bike['bike_id']));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('latitude', mode: InputOption::VALUE_OPTIONAL, default: self::DEFAULT_LATITUDE);
        $this->addOption('longitude', mode: InputOption::VALUE_OPTIONAL, default: self::DEFAULT_LONGITUDE);
    }
}
