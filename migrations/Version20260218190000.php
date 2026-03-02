<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260218190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create match_lineup table for match compositions';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE match_lineup (id INT AUTO_INCREMENT NOT NULL, matchs_id INT NOT NULL, joueur_id INT NOT NULL, type VARCHAR(50) NOT NULL, buts INT NULL, cartons_jaunes INT NULL, cartons_rouges INT NULL, INDEX IDX_2C93DE0D3DFCD3B1 (matchs_id), INDEX IDX_2C93DE0D8D6E3B8C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE match_lineup ADD CONSTRAINT FK_2C93DE0D3DFCD3B1 FOREIGN KEY (matchs_id) REFERENCES matchs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE match_lineup ADD CONSTRAINT FK_2C93DE0D8D6E3B8C FOREIGN KEY (joueur_id) REFERENCES joueur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE match_lineup DROP FOREIGN KEY FK_2C93DE0D3DFCD3B1');
        $this->addSql('ALTER TABLE match_lineup DROP FOREIGN KEY FK_2C93DE0D8D6E3B8C');
        $this->addSql('DROP TABLE match_lineup');
    }
}
