<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260219000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position fields to match_lineup';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE match_lineup ADD position_x DOUBLE PRECISION DEFAULT NULL, ADD position_y DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE match_lineup DROP position_x, DROP position_y');
    }
}
