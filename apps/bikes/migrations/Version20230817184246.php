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
                bike_id SERIAL PRIMARY KEY,
                registration_correlation_id UUID,
                is_active BOOL,
                UNIQUE (registration_correlation_id)
            );
        ');

        $this->addSql('
            CREATE TABLE bikes.tracking (
                id SERIAL PRIMARY KEY,
                bike_id INT NOT NULL REFERENCES bikes.bikes (bike_id),
                location JSONB NOT NULL,
                tracked_at TIMESTAMPTZ NOT NULL
            );
        ');

        $this->addSql('CREATE INDEX tracking_bike_id ON bikes.tracking (bike_id);');
    }
}
