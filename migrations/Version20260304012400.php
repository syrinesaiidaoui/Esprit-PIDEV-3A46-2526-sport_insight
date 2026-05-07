<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260304012400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing user columns cv_name and updated_at after merge.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD COLUMN IF NOT EXISTS cv_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS cv_name');
        $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS updated_at');
    }
}
