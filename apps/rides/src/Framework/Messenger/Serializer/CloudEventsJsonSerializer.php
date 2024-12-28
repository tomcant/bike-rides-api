<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Serializer;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;
use BikeRides\SharedKernel\Domain\Event\DomainEventFactory;
use CloudEvents\Serializers\Normalizers\V1\Denormalizer;
use CloudEvents\Serializers\Normalizers\V1\Normalizer;
use CloudEvents\V1\CloudEventImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final readonly class CloudEventsJsonSerializer implements SerializerInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /** @return array<string, mixed> */
    public function encode(Envelope $envelope): array
    {
        /** @var DomainEvent $event */
        $event = $envelope->getMessage();
        $type = "rides.{$event->type()}.v{$event->version()}";

        $cloudEvent = new CloudEventImmutable(
            id: $event->id,
            source: 'rides-api',
            type: $type,
            data: Json::decode($event->serialize()),
            time: $event->occurredAt,
        );

        return [
            'body' => (new Normalizer())->normalize($cloudEvent, rawData: false),
        ];
    }

    /** @param array<mixed, mixed> $encodedEnvelope */
    public function decode(array $encodedEnvelope): Envelope
    {
        $this->logger->info(self::class, ['encodedEnvelope' => $encodedEnvelope]);

        $decodedBody = Json::decode($encodedEnvelope['body']);
        $cloudEvent = (new Denormalizer())->denormalize($decodedBody['detail']);

        return new Envelope(DomainEventFactory::fromCloudEvent($cloudEvent));
    }
}
