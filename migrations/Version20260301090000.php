<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260301090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add adresse column to equipe';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE equipe ADD COLUMN IF NOT EXISTS adresse VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE equipe DROP COLUMN IF EXISTS adresse');
    }
}
