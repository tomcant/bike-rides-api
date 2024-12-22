<?php

declare(strict_types=1);

namespace BikeRides\Foundation\Domain;

interface AggregateEvent
{
    public function getEventName(): string;

    public function getAggregateName(): AggregateName;

    public function getAggregateId(): EntityId;

    public function getAggregateVersion(): AggregateVersion;

    public function serialize(): string;

    public static function deserialize(string $serialized): self;
}
