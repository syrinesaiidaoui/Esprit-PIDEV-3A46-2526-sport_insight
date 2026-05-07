<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create missing chat_message and message tables used by chat features.
 */
final class Version20260304093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add chat_message and message tables for user messaging features.';
    }

    public function up(Schema $schema): void
    {
        // chat_message table (front-office annonce chat)
        $this->addSql(<<<'SQL'
CREATE TABLE IF NOT EXISTS chat_message (
    id INT AUTO_INCREMENT NOT NULL,
    auteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    annonce_id INT NOT NULL,
    message LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    notification_sent TINYINT(1) NOT NULL DEFAULT 0,
    INDEX IDX_BEF72EB160BB6FE6 (auteur_id),
    INDEX IDX_BEF72EB1D3A0D5D7 (destinataire_id),
    INDEX IDX_BEF72EB18805AB2F (annonce_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB
SQL);

        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_BEF72EB160BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_BEF72EB1D3A0D5D7 FOREIGN KEY (destinataire_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_BEF72EB18805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE');

        // message table (direct user-to-user chat)
        $this->addSql(<<<'SQL'
CREATE TABLE IF NOT EXISTS message (
    id INT AUTO_INCREMENT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content LONGTEXT NOT NULL,
    sent_at DATETIME NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    INDEX IDX_B6BD307FF624B39D (sender_id),
    INDEX IDX_B6BD307FCD53EDB6 (receiver_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB
SQL);

        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_BEF72EB160BB6FE6');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_BEF72EB1D3A0D5D7');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_BEF72EB18805AB2F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('DROP TABLE IF EXISTS chat_message');
        $this->addSql('DROP TABLE IF EXISTS message');
    }
}
