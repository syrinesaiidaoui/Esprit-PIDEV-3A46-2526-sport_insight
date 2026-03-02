<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260218200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make lineup columns nullable';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matchs CHANGE lineup_domicile lineup_domicile LONGTEXT DEFAULT NULL, CHANGE lineup_exterieur lineup_exterieur LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matchs CHANGE lineup_domicile lineup_domicile LONGTEXT NOT NULL, CHANGE lineup_exterieur lineup_exterieur LONGTEXT DEFAULT NULL');
    }
}
