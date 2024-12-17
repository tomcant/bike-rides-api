<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230817184246 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA bikes');

        $this->addSql('
            CREATE TABLE bikes.bikes (
                bike_id UUID PRIMARY KEY,
                location JSONB,
                is_active BOOL
            );
        ');

        $this->addSql('
            CREATE TABLE bikes.tracking (
                id SERIAL PRIMARY KEY,
                bike_id UUID NOT NULL,
                location JSONB NOT NULL,
                tracked_at TIMESTAMPTZ NOT NULL
            );
        ');
    }
}
