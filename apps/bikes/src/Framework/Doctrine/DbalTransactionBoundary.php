<?php

declare(strict_types=1);

namespace App\Framework\Doctrine;

use BikeRides\Foundation\Domain\TransactionBoundary;
use Doctrine\DBAL\Connection;

final readonly class DbalTransactionBoundary implements TransactionBoundary
{
    public function __construct(private Connection $connection)
    {
    }

    public function begin(): void
    {
        $this->connection->beginTransaction();
    }

    public function end(): void
    {
        $this->connection->commit();
    }

    public function abort(): void
    {
        $this->connection->rollBack();
    }
}
