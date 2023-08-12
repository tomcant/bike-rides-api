<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Infrastructure;

use App\BikeRides\Shared\Application\Command\Command;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\BikeRides\Shared\Application\Command\CommandNotRegistered;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyCommandBus implements CommandBus
{
    public function __construct(
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
        $this->connection->executeStatement(
            '
                INSERT INTO public.command_log (command_name, command_data, dispatched_at)
                VALUES (:commandName, :commandData, CURRENT_TIMESTAMP)
            ',
            [
                'commandName' => $command::class,
                'commandData' => $command->serialize(),
            ],
        );
    }
}
