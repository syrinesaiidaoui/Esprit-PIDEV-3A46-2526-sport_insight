<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260301100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Backfill missing columns used by current Doctrine mappings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE equipe
            ADD COLUMN IF NOT EXISTS adresse VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS telephone VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE sponsor
            ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL');

        $this->addSql('ALTER TABLE matchs
            ADD COLUMN IF NOT EXISTS id_match VARCHAR(100) NOT NULL DEFAULT \'\'');

        $this->addSql('ALTER TABLE `order`
            ADD COLUMN IF NOT EXISTS size VARCHAR(10) DEFAULT NULL');

        $this->addSql("ALTER TABLE contrat_sponsor
            ADD COLUMN IF NOT EXISTS statut VARCHAR(50) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS notified TINYINT(1) NOT NULL DEFAULT 0,
            ADD COLUMN IF NOT EXISTS statut_paiement VARCHAR(50) NOT NULL DEFAULT 'Non paye'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE equipe DROP COLUMN IF EXISTS adresse');
        $this->addSql('ALTER TABLE equipe DROP COLUMN IF EXISTS telephone');
        $this->addSql('ALTER TABLE equipe DROP COLUMN IF EXISTS email');

        $this->addSql('ALTER TABLE sponsor DROP COLUMN IF EXISTS updated_at');
        $this->addSql('ALTER TABLE matchs DROP COLUMN IF EXISTS id_match');
        $this->addSql('ALTER TABLE `order` DROP COLUMN IF EXISTS size');
        $this->addSql('ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS statut');
        $this->addSql('ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS notified');
        $this->addSql('ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS statut_paiement');
    }
}
