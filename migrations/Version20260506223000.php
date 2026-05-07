<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260506223000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add stored odds snapshot columns to matchs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE matchs ADD COLUMN IF NOT EXISTS odds_snapshot_json LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)'");
        $this->addSql("ALTER TABLE matchs ADD COLUMN IF NOT EXISTS odds_source VARCHAR(120) DEFAULT NULL");
        $this->addSql("ALTER TABLE matchs ADD COLUMN IF NOT EXISTS odds_synced_at DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE matchs DROP COLUMN IF EXISTS odds_synced_at");
        $this->addSql("ALTER TABLE matchs DROP COLUMN IF EXISTS odds_source");
        $this->addSql("ALTER TABLE matchs DROP COLUMN IF EXISTS odds_snapshot_json");
    }
}
