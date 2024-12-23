<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Integration\Infrastructure;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class PostgresTestCase extends KernelTestCase
{
    protected Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = self::getContainer()->get(Connection::class);

        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();

        parent::tearDown();
    }
}
