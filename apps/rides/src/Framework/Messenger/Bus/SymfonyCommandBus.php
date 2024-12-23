<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Bus;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Application\Command\CommandNotRegistered;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Timestamp;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyCommandBus implements CommandBus
{
    public function __construct(
        #[Autowire(service: 'command.bus')]
        private MessageBusInterface $bus,
        private Connection $connection,
    ) {
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->log($command);
            $this->bus->dispatch($command);
        } catch (NoHandlerForMessageException) {
            throw new CommandNotRegistered($command);
        } catch (HandlerFailedException $exception) {
            while ($exception instanceof HandlerFailedException) {
                $exception = $exception->getPrevious();
            }

            if (null !== $exception) {
                throw $exception;
            }
        }
    }

    private function log(Command $command): void
    {
        $statement = $this->connection->prepare('
            INSERT INTO public.command_log (command_name, command_data, dispatched_at)
            VALUES (:command_name, :command_data, :dispatched_at)
        ');

        $statement->bindValue('command_name', $command::class);
        $statement->bindValue('command_data', $command->serialize());
        $statement->bindValue('dispatched_at', Timestamp::format(Clock::now()));

        $statement->executeStatement();
    }
}
