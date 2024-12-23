<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Cli\Fixture;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'rides:fixture:rider')]
final class RiderFixtureCommand extends FixtureCommand
{
    private const string DEFAULT_RIDER_ID = 'rider_id';

    public function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $riderId = (string) $input->getOption('rider-id');

        $this->postJson('/rides/rider', ['rider_id' => $riderId]);

        $output->writeln(\sprintf("\nRider ID: <info>%s</info>\n", $riderId));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('rider-id', mode: InputOption::VALUE_OPTIONAL, default: self::DEFAULT_RIDER_ID);
    }
}
