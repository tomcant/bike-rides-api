<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Domain\Helpers;

final class AggregateEvents implements \IteratorAggregate
{
    /** @param AggregateEvent[] $events */
    public function __construct(private array $events)
    {
    }

    public function add(AggregateEvent $event): self
    {
        return new self([...$this->events, $event]);
    }

    public static function make(): self
    {
        return new self([]);
    }

    public function merge(self $that): self
    {
        return new self([...$this->events, ...$that->events]);
    }

    public function isEmpty(): bool
    {
        return empty($this->events);
    }

    /** @return \Traversable<AggregateEvent> */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->events);
    }

    public function reduce(callable $function, $initial)
    {
        return \array_reduce($this->events, $function, $initial);
    }
}
