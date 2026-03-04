<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260304021600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align commentaire table with entity fields (auteur_anonyme, nb_likes, moderation_status, moderation_reason).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS auteur_anonyme VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS nb_likes INT NOT NULL DEFAULT 0');
        $this->addSql("ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS moderation_status VARCHAR(20) NOT NULL DEFAULT 'PENDING'");
        $this->addSql('ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS moderation_reason LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commentaire DROP COLUMN IF EXISTS moderation_reason');
        $this->addSql('ALTER TABLE commentaire DROP COLUMN IF EXISTS moderation_status');
        $this->addSql('ALTER TABLE commentaire DROP COLUMN IF EXISTS nb_likes');
        $this->addSql('ALTER TABLE commentaire DROP COLUMN IF EXISTS auteur_anonyme');
    }
}

