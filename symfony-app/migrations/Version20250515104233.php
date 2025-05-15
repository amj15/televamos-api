<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515104233 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE messenger_messages (
                id BIGSERIAL PRIMARY KEY,
                body TEXT NOT NULL,
                headers TEXT NOT NULL,
                queue VARCHAR(255) NOT NULL,
                created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
                available_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
                delivered_at TIMESTAMP(6) DEFAULT NULL
            );
SQL
        );

        $this->addSql('CREATE INDEX IDX_messenger_messages_queue_available_at ON messenger_messages (queue, available_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE messenger_messages');
    }
}
