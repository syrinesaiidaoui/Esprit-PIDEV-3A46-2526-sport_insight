<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260303154000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add adresse column to sponsor table for map geocoding.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sponsor ADD COLUMN IF NOT EXISTS adresse VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sponsor DROP COLUMN IF EXISTS adresse');
    }
}
