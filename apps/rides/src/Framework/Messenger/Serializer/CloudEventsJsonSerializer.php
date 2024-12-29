<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Serializer;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Event\DomainEventFactory;
use CloudEvents\Serializers\Normalizers\V1\Denormalizer;
use CloudEvents\Serializers\Normalizers\V1\Normalizer;
use CloudEvents\V1\CloudEventImmutable;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final readonly class CloudEventsJsonSerializer implements SerializerInterface
{
    /** @return array<string, mixed> */
    public function encode(Envelope $envelope): array
    {
        /** @var DomainEvent $event */
        $event = $envelope->getMessage();
        $type = "bike-rides.{$event->type()}.v{$event->version()}";

        $cloudEvent = new CloudEventImmutable(
            id: $event->id,
            source: 'rides-api',
            type: $type,
            data: Json::decode($event->serialize()),
            time: $event->occurredAt,
        );

        return [
            'body' => Json::encode((new Normalizer())->normalize($cloudEvent, rawData: false)),
            'headers' => ['type' => $type],
        ];
    }

    /** @param array<mixed, mixed> $encodedEnvelope */
    public function decode(array $encodedEnvelope): Envelope
    {
        $decodedBody = Json::decode($encodedEnvelope['body']);
        $cloudEvent = (new Denormalizer())->denormalize(Json::decode($decodedBody['detail']['body']));

        return new Envelope(
            message: DomainEventFactory::fromCloudEvent($cloudEvent),
            stamps: [new BusNameStamp('domain_event.bus')],
        );
    }
}
