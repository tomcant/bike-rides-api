<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Functional\UserInterface;

use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Clock\ClockStub;
use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\Foundation\Json;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        static::getContainer()->get(DomainEventBus::class)->publish($event);
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
