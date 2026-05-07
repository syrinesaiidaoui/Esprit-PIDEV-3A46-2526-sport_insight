<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Safely align DB schema with entities when legacy tables already exist.
 */
final class Version20260304100500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing columns/tables (contrat_sponsor.statut, statut_paiement, joueur, match_lineup, etc.) without recreating existing tables.';
    }

    public function up(Schema $schema): void
    {
        // contrat_sponsor missing fields
        $this->addSql("ALTER TABLE contrat_sponsor ADD COLUMN IF NOT EXISTS statut VARCHAR(50) DEFAULT NULL");
        $this->addSql("ALTER TABLE contrat_sponsor ADD COLUMN IF NOT EXISTS statut_paiement VARCHAR(50) NOT NULL DEFAULT 'Non payé'");

        // equipe optional columns
        $this->addSql("ALTER TABLE equipe ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL");

        // matchs additional fields
        $this->addSql("ALTER TABLE matchs ADD COLUMN IF NOT EXISTS score_equipe_domicile INT DEFAULT NULL");
        $this->addSql("ALTER TABLE matchs ADD COLUMN IF NOT EXISTS score_equipe_exterieur INT DEFAULT NULL");
        $this->addSql("ALTER TABLE matchs MODIFY lineup_domicile LONGTEXT DEFAULT NULL");

        // product optional fields
        $this->addSql("ALTER TABLE product ADD COLUMN IF NOT EXISTS size VARCHAR(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE product ADD COLUMN IF NOT EXISTS brand VARCHAR(30) DEFAULT NULL");
        $this->addSql("ALTER TABLE product ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL");

        // order extra fields
        $this->addSql("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS size VARCHAR(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS payment_method VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS payment_status VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS contact_email VARCHAR(180) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(30) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` ADD COLUMN IF NOT EXISTS total_amount NUMERIC(12, 2) DEFAULT NULL");

        // sponsor optional fields
        $this->addSql("ALTER TABLE sponsor ADD COLUMN IF NOT EXISTS logo_name VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sponsor ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE sponsor ADD COLUMN IF NOT EXISTS adresse VARCHAR(255) DEFAULT NULL");

        // commentaire optional fields in case earlier migration failed
        $this->addSql("ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS auteur_anonyme VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS nb_likes INT NOT NULL DEFAULT 0");
        $this->addSql("ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS moderation_status VARCHAR(20) NOT NULL DEFAULT 'PENDING'");
        $this->addSql("ALTER TABLE commentaire ADD COLUMN IF NOT EXISTS moderation_reason LONGTEXT DEFAULT NULL");

        // user optional fields
        $this->addSql("ALTER TABLE user ADD COLUMN IF NOT EXISTS cv_name VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE user ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT NULL");

        // create joueur table if missing
        $this->addSql(<<<'SQL'
CREATE TABLE IF NOT EXISTS joueur (
    id INT AUTO_INCREMENT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    numero INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    equipe_id INT NOT NULL,
    INDEX IDX_FD71A9C56D861B89 (equipe_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB
SQL);

        // create match_lineup table if missing
        $this->addSql(<<<'SQL'
CREATE TABLE IF NOT EXISTS match_lineup (
    id INT AUTO_INCREMENT NOT NULL,
    type VARCHAR(50) NOT NULL,
    buts INT DEFAULT NULL,
    cartons_jaunes INT DEFAULT NULL,
    cartons_rouges INT DEFAULT NULL,
    position_x DOUBLE PRECISION DEFAULT NULL,
    position_y DOUBLE PRECISION DEFAULT NULL,
    matchs_id INT NOT NULL,
    joueur_id INT NOT NULL,
    INDEX IDX_6C11F7CB88EB7468 (matchs_id),
    INDEX IDX_6C11F7CBA9E2D76C (joueur_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB
SQL);

        // foreign keys for new tables (ignored if already exist)
        $this->addSql("ALTER TABLE match_lineup DROP FOREIGN KEY IF EXISTS FK_6C11F7CB88EB7468");
        $this->addSql("ALTER TABLE match_lineup DROP FOREIGN KEY IF EXISTS FK_6C11F7CBA9E2D76C");
        $this->addSql("ALTER TABLE joueur DROP FOREIGN KEY IF EXISTS FK_FD71A9C56D861B89");

        $this->addSql("ALTER TABLE match_lineup ADD CONSTRAINT FK_6C11F7CB88EB7468 FOREIGN KEY (matchs_id) REFERENCES matchs (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE match_lineup ADD CONSTRAINT FK_6C11F7CBA9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueur (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C56D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema): void
    {
        // We only drop what we created to avoid data loss.
        $this->addSql("ALTER TABLE match_lineup DROP FOREIGN KEY FK_6C11F7CB88EB7468");
        $this->addSql("ALTER TABLE match_lineup DROP FOREIGN KEY FK_6C11F7CBA9E2D76C");
        $this->addSql("DROP TABLE IF EXISTS match_lineup");
        $this->addSql("ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C56D861B89");
        $this->addSql("DROP TABLE IF EXISTS joueur");

        $this->addSql("ALTER TABLE commentaire DROP COLUMN IF EXISTS moderation_reason");
        $this->addSql("ALTER TABLE commentaire DROP COLUMN IF EXISTS moderation_status");
        $this->addSql("ALTER TABLE commentaire DROP COLUMN IF EXISTS nb_likes");
        $this->addSql("ALTER TABLE commentaire DROP COLUMN IF EXISTS auteur_anonyme");

        $this->addSql("ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS statut_paiement");
        $this->addSql("ALTER TABLE contrat_sponsor DROP COLUMN IF EXISTS statut");

        $this->addSql("ALTER TABLE matchs DROP COLUMN IF EXISTS score_equipe_exterieur");
        $this->addSql("ALTER TABLE matchs DROP COLUMN IF EXISTS score_equipe_domicile");

        $this->addSql("ALTER TABLE product DROP COLUMN IF EXISTS image");
        $this->addSql("ALTER TABLE product DROP COLUMN IF EXISTS brand");
        $this->addSql("ALTER TABLE product DROP COLUMN IF EXISTS size");

        $this->addSql("ALTER TABLE `order` DROP COLUMN IF EXISTS total_amount");
        $this->addSql("ALTER TABLE `order` DROP COLUMN IF EXISTS contact_phone");
        $this->addSql("ALTER TABLE `order` DROP COLUMN IF EXISTS contact_email");
        $this->addSql("ALTER TABLE `order` DROP COLUMN IF EXISTS payment_status");
        $this->addSql("ALTER TABLE `order` DROP COLUMN IF EXISTS payment_method");
        $this->addSql("ALTER TABLE `order` DROP COLUMN IF EXISTS size");

        $this->addSql("ALTER TABLE sponsor DROP COLUMN IF EXISTS adresse");
        $this->addSql("ALTER TABLE sponsor DROP COLUMN IF EXISTS updated_at");
        $this->addSql("ALTER TABLE sponsor DROP COLUMN IF EXISTS logo_name");

        $this->addSql("ALTER TABLE user DROP COLUMN IF EXISTS updated_at");
        $this->addSql("ALTER TABLE user DROP COLUMN IF EXISTS cv_name");
    }
}
