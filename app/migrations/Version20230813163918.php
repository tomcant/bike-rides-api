<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230813163918 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE public.command_log (
                id SERIAL PRIMARY KEY,
                command_name VARCHAR NOT NULL,
                command_data JSONB NOT NULL,
                dispatched_at TIMESTAMPTZ NOT NULL
            );
        ');

        $this->addSql('
            CREATE TABLE public.domain_event_log (
                id SERIAL PRIMARY KEY,
                event_name VARCHAR NOT NULL,
                event_data JSONB NOT NULL,
                occurred_at TIMESTAMPTZ NOT NULL
            );
        ');
    }
}
