<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add image column to joueur table
 */
final class Version20260218180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image column to joueur table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE joueur ADD image VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE joueur DROP COLUMN image');
    }
}
