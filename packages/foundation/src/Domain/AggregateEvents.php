<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

/** @implements \IteratorAggregate<AggregateEvent> */
final readonly class AggregateEvents implements \IteratorAggregate
{
    /** @param list<AggregateEvent> $events */
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

    public function reduce(callable $function, mixed $initial): mixed
    {
        return \array_reduce($this->events, $function, $initial);
    }
}
