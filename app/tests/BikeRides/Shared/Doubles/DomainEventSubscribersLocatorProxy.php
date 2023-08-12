<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Doubles;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;

final class DomainEventSubscribersLocatorProxy implements HandlersLocatorInterface
{
    private ?string $onlyNamespace = null;

    public function __construct(private readonly HandlersLocatorInterface $locator)
    {
    }

    public function onlyNamespace(string $namespace): void
    {
        $this->onlyNamespace = $namespace;
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        $handlers = $this->locator->getHandlers($envelope);

        if ($this->onlyNamespace === null) {
            return $handlers;
        }

        return \array_filter(
            \iterator_to_array($handlers),
            fn (HandlerDescriptor $h) => \str_starts_with($h->getName(), $this->onlyNamespace),
        );
    }
}
