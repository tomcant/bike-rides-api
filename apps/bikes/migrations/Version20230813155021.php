<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230813155021 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE public.messenger_messages (
                id BIGSERIAL NOT NULL PRIMARY KEY,
                body TEXT NOT NULL,
                headers TEXT NOT NULL,
                queue_name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP(0) NOT NULL,
                available_at TIMESTAMP(0) NOT NULL,
                delivered_at TIMESTAMP(0) DEFAULT NULL::TIMESTAMP WITHOUT TIME ZONE
            );
        ');

        $this->addSql('CREATE INDEX messenger_messages_queue_name ON public.messenger_messages (queue_name);');
        $this->addSql('CREATE INDEX messenger_messages_available_at ON public.messenger_messages (available_at);');
        $this->addSql('CREATE INDEX messenger_messages_delivered_at ON public.messenger_messages (delivered_at);');
    }
}
