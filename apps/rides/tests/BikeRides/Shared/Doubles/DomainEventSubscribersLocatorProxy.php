<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;

final class DomainEventSubscribersLocatorProxy implements HandlersLocatorInterface
{
    private ?string $subscriberNamespace = null;

    public function __construct(private readonly HandlersLocatorInterface $locator)
    {
    }

    public function restrictSubscribersByNamespace(string $namespace): void
    {
        $this->subscriberNamespace = $namespace;
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        $handlers = $this->locator->getHandlers($envelope);

        if (null === $this->subscriberNamespace) {
            return $handlers;
        }

        return \array_filter(
            \iterator_to_array($handlers),
            fn (HandlerDescriptor $handler) => \str_starts_with($handler->getName(), $this->subscriberNamespace),
        );
    }
}
