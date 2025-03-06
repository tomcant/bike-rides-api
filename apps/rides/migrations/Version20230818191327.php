<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230818191327 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA rides');

        $this->addSql('
            CREATE TABLE rides.event_store (
                id SERIAL PRIMARY KEY,
                aggregate_name VARCHAR NOT NULL,
                aggregate_id UUID NOT NULL,
                aggregate_version INT NOT NULL,
                event_name VARCHAR NOT NULL,
                event_data JSONB NOT NULL,
                UNIQUE (aggregate_name, aggregate_id, aggregate_version)
            );
        ');

        $this->addSql('CREATE INDEX event_store_aggregate ON rides.event_store (aggregate_name, aggregate_id);');

        $this->addSql('
            CREATE TABLE rides.riders (
                rider_id VARCHAR PRIMARY KEY
            );
        ');

        $this->addSql('
            CREATE TABLE rides.bikes (
                bike_id INT PRIMARY KEY,
                location JSONB NOT NULL
            );
        ');

        $this->addSql('
            CREATE TABLE rides.summaries (
                ride_id UUID PRIMARY KEY,
                duration JSONB NOT NULL,
                route JSONB NOT NULL
            );
        ');

        $this->addSql('
            CREATE TABLE rides.projection_ride (
                ride_id UUID PRIMARY KEY,
                rider_id VARCHAR NOT NULL,
                bike_id INT NOT NULL,
                started_at TIMESTAMPTZ NOT NULL,
                ended_at TIMESTAMPTZ
            );
        ');
    }
}
