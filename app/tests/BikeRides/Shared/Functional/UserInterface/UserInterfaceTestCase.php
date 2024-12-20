<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Functional\UserInterface;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;
use App\Foundation\Clock\Clock;
use App\Foundation\Json;
use App\Tests\BikeRides\Shared\Doubles\ClockStub;
use App\Tests\BikeRides\Shared\Doubles\DomainEventSubscribersLocatorProxy;
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

        self::restrictDomainEventSubscribersByBoundedContext();

        Clock::useClock(new ClockStub());
    }

    protected function tearDown(): void
    {
        static::getContainer()->get('database_connection')->rollBack();

        $this->client->enableReboot();

        parent::tearDown();
    }

    protected function publishEvent(DomainEvent $event): void
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

    /*
     * Since we're modelling multiple Bounded Contexts in a single app, it's useful to restrict the event subscribers
     * during UI tests to maintain isolation and avoid triggering multiple contexts.
     *
     * By convention, the relevant subscriber namespace can be determined by removing '\Tests' from the test namespace.
     * E.g. '\App\Tests\BikeRides\Billing\Functional\UserInterface' => '\App\BikeRides\Billing\UserInterface'
     */
    private static function restrictDomainEventSubscribersByBoundedContext(): void
    {
        $testNamespace = \mb_substr(static::class, 0, \mb_strrpos(static::class, '\\'));
        $subscriberNamespace = \str_replace(['\\Tests\\', '\\Functional\\'], '\\', $testNamespace);

        static::getContainer()
            ->get(DomainEventSubscribersLocatorProxy::class)
            ->restrictSubscribersByNamespace($subscriberNamespace);
    }
}
