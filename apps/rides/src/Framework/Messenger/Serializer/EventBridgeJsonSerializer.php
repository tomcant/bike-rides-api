<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Serializer;

use BikeRides\Foundation\Json;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final readonly class EventBridgeJsonSerializer implements SerializerInterface
{
    public function __construct(
        #[Autowire(service: CloudEventsJsonSerializer::class)]
        private SerializerInterface $wrappedSerializer,
    ) {
    }

    /** @return array<string, mixed> */
    public function encode(Envelope $envelope): array
    {
        throw new \RuntimeException();
    }

    /** @param array<mixed, mixed> $encodedEnvelope */
    public function decode(array $encodedEnvelope): Envelope
    {
        $decodedBody = Json::decode($encodedEnvelope['body']);

        return $this->wrappedSerializer->decode($decodedBody['detail']);
    }
}
