<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create Joueur table and add joueurs relationship to equipe table
 */
final class Version20260218170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Joueur table and add joueurs relationship to Equipe';
    }

    public function up(Schema $schema): void
    {
        // Create joueur table
        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, equipe_id INT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, numero INT NOT NULL, INDEX IDX_FD71A9C5532C8C37 (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraint
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C5532C8C37 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C5532C8C37');
        $this->addSql('DROP TABLE joueur');
    }
}
