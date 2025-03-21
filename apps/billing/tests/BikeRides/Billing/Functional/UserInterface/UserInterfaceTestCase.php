<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Functional\UserInterface;

use App\Framework\Messenger\Serializer\CloudEventsJsonSerializer;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Clock\ClockStub;
use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Json;
use Bref\Context\Context;
use Bref\Symfony\Messenger\Service\Sqs\SqsConsumer;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;

abstract class UserInterfaceTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->client->disableReboot();

        static::getContainer()->get('database_connection')->beginTransaction();

        Clock::useClock(new ClockStub());
    }

    protected function tearDown(): void
    {
        static::getContainer()->get('database_connection')->rollBack();

        $this->client->enableReboot();

        parent::tearDown();
    }

    protected function handleDomainEvent(DomainEvent $event): void
    {
        /** @var CloudEventsJsonSerializer $serializer */
        $serializer = self::getContainer()->get(CloudEventsJsonSerializer::class);
        $encodedEnvelope = $serializer->encode(Envelope::wrap($event));
        $eventBridgePayload = \json_encode(['detail' => $encodedEnvelope]);

        /** @var SqsConsumer $sqsConsumer */
        $sqsConsumer = self::getContainer()->get(SqsConsumer::class);
        $sqsConsumer->handle(
            [
                'Records' => [
                    [
                        'body' => $eventBridgePayload,
                        'messageId' => '00000000-0000-0000-0000-000000000000',
                        'receiptHandle' => 'receiptHandle',
                        'attributes' => ['ApproximateReceiveCount' => 1],
                        'messageAttributes' => [],
                        'eventSource' => 'aws:sqs',
                        'eventSourceARN' => 'arn:aws:sqs:eu-west-1:000000000000:queue-name',
                        'awsRegion' => 'eu-west-1',
                    ],
                ],
            ],
            Context::fake(),
        );
    }

    /** @return array<mixed, mixed> */
    protected function getJson(string $url, bool $assertResponseIsSuccessful = true): array
    {
        $this->client->request('GET', $url);

        if ($assertResponseIsSuccessful) {
            self::assertResponseIsSuccessful();
        }

        return Json::decode($this->client->getResponse()->getContent());
    }

    /**
     * @param array<mixed, mixed> $body
     *
     * @return ?array<mixed, mixed>
     */
    protected function postJson(string $url, array $body = [], bool $assertResponseIsSuccessful = true): ?array
    {
        $this->client->request(
            'POST',
            $url,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: Json::encode($body),
        );

        if ($assertResponseIsSuccessful) {
            self::assertResponseIsSuccessful();
        }

        $content = $this->client->getResponse()->getContent();

        return !empty($content) ? Json::decode($content, options: 0) : null; // remove `options: 0` when API returns JSON instead of HTML for errors
    }

    protected function parseResponseLinkUrl(): string
    {
        $link = $this->client->getResponse()->headers->get('Link');

        \preg_match('/<(.+)>; rel="[^\"]+"/', $link, $matches);

        return $matches[1];
    }

    /**
     * @param non-empty-array<string, mixed> $array
     * @param non-empty-list<string>         $keys
     */
    protected static function assertArrayHasKeys(array $array, array $keys): void
    {
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $array);
        }
    }
}
