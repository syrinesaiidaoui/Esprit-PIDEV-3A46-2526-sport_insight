<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260228132000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Backfill missing contrat_sponsor status columns and notification table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE contrat_sponsor
            ADD COLUMN IF NOT EXISTS statut VARCHAR(50) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS notified TINYINT(1) NOT NULL DEFAULT 0,
            ADD COLUMN IF NOT EXISTS statut_paiement VARCHAR(50) NOT NULL DEFAULT 'Non paye'");

        $this->addSql("CREATE TABLE IF NOT EXISTS notification (
            id INT AUTO_INCREMENT NOT NULL,
            message VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            is_read TINYINT(1) NOT NULL,
            user_id INT DEFAULT NULL,
            INDEX IDX_BF5476CAA76ED395 (user_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS notification');
        $this->addSql('ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS statut');
        $this->addSql('ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS notified');
        $this->addSql('ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS statut_paiement');
    }
}
