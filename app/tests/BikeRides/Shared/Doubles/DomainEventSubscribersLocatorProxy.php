<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;

final class DomainEventSubscribersLocatorProxy implements HandlersLocatorInterface
{
    private ?string $subscriberNamespace = null;
    private ?string $eventClass = null;

    public function __construct(private readonly HandlersLocatorInterface $locator)
    {
    }

    public function restrictSubscriberNamespace(string $namespace): void
    {
        $this->subscriberNamespace = $namespace;
    }

    public function restrictEventClass(string $class): void
    {
        $this->eventClass = $class;
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        $handlers = $this->locator->getHandlers($envelope);

        if ($this->subscriberNamespace === null && $this->eventClass === null) {
            return $handlers;
        }

        return \array_filter(
            \iterator_to_array($handlers),
            function (HandlerDescriptor $handler) use ($envelope) {
                if (
                    $this->subscriberNamespace !== null
                    && ! \str_starts_with($handler->getName(), $this->subscriberNamespace)
                ) {
                    return false;
                }

                return $this->eventClass === null || $this->eventClass === $envelope->getMessage()::class;
            },
        );
    }
}
