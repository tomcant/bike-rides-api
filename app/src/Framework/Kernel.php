<?php

declare(strict_types=1);

namespace App\Framework;

use App\BikeRides\Shared\Application\Command\CommandHandler;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventsSubscriber;
use App\BikeRides\Shared\Domain\Helpers\DomainEventSubscriber;
use Bref\SymfonyBridge\BrefKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Kernel extends BrefKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(CommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command.bus']);

        $container
            ->registerForAutoconfiguration(AggregateEventsSubscriber::class)
            ->addTag('messenger.message_handler', ['bus' => 'aggregate_events.bus']);

        $container
            ->registerForAutoconfiguration(DomainEventSubscriber::class)
            ->addTag('messenger.message_handler', ['bus' => 'domain_event.bus']);
    }
}
