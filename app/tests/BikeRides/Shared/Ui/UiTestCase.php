<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Ui;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;
use App\Foundation\Clock\Clock;
use App\Tests\BikeRides\Shared\Doubles\ClockStub;
use App\Tests\BikeRides\Shared\Doubles\DomainEventSubscribersLocatorProxy;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class UiTestCase extends WebTestCase
{
    protected readonly KernelBrowser $client;
    protected readonly Clock $clock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->client->disableReboot();

        static::getContainer()->get('database_connection')->beginTransaction();

        static::restrictDomainEventSubscribersByTestCase();

        Clock::useClock($this->clock = new ClockStub());
    }

    protected function tearDown(): void
    {
        static::getContainer()->get('database_connection')->rollBack();

        $this->client->enableReboot();

        parent::tearDown();
    }

    protected function handleEvent(DomainEvent $event): void
    {
        static::getContainer()->get(DomainEventBus::class)->publish($event);
    }

    protected function getJson(string $url, bool $assertResponseIsSuccessful = true): array
    {
        $this->client->request('GET', $url);

        if ($assertResponseIsSuccessful) {
            self::assertResponseIsSuccessful();
        }

        return \json_decode_array($this->client->getResponse()->getContent());
    }

    protected function postJson(string $url, array $body = [], bool $assertResponseIsSuccessful = true): ?array
    {
        $this->client->request(
            'POST',
            $url,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: \json_encode_array($body),
        );

        if ($assertResponseIsSuccessful) {
            self::assertResponseIsSuccessful();
        }

        $content = $this->client->getResponse()->getContent();

        return ! empty($content) ? \json_decode_array($content, options: 0) : null; // remove `options: 0` when API returns JSON instead of HTML for errors
    }

    protected function parseResponseLinkUrl(): string
    {
        $link = $this->client->getResponse()->headers->get('Link');

        \preg_match('/<(.+)>; rel="[^\"]+"/', $link, $matches);

        return $matches[1];
    }

    protected static function assertArrayHasKeys(array $array, array $keys): void
    {
        foreach ($keys as $key) {
            static::assertArrayHasKey($key, $array);
        }
    }

    private static function restrictDomainEventSubscribersByTestCase(): void
    {
        $testNamespace = \mb_substr(static::class, 0, \mb_strrpos(static::class, '\\'));
        $subscribersNamespace = \str_replace('\\Tests\\', '\\', $testNamespace);

        static::getContainer()->get(DomainEventSubscribersLocatorProxy::class)->onlyNamespace($subscribersNamespace);
    }
}
