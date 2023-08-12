<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231001151431 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA billing');

        $this->addSql('
            CREATE TABLE billing.event_store (
                id SERIAL PRIMARY KEY,
                aggregate_name VARCHAR NOT NULL,
                aggregate_id UUID NOT NULL,
                aggregate_version INT NOT NULL,
                event_name VARCHAR NOT NULL,
                event_data JSONB NOT NULL,
                UNIQUE (aggregate_name, aggregate_id, aggregate_version)
            );
        ');

        $this->addSql('
            CREATE INDEX event_store_aggregate ON billing.event_store (aggregate_name, aggregate_id);
        ');

        $this->addSql('
            CREATE TABLE billing.projection_ride_payment (
                ride_payment_id UUID NOT NULL,
                ride_id VARCHAR NOT NULL,
                total_price JSONB NOT NULL,
                price_per_minute JSONB NOT NULL,
                initiated_at TIMESTAMPTZ NOT NULL,
                PRIMARY KEY (ride_payment_id)
            );
        ');

        $this->addSql('CREATE INDEX projection_ride_payment_ride_id ON billing.projection_ride_payment (ride_id);');
    }
}
