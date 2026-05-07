-- Additive shared-schema repair for the Symfony and JavaFX Sport Insight apps.
-- This script intentionally avoids DROP, TRUNCATE, DELETE, and destructive ALTERs.

CREATE TABLE IF NOT EXISTS `entrainement_user` (
    `entrainement_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    INDEX `IDX_EB3D3F70A15E8FD` (`entrainement_id`),
    INDEX `IDX_EB3D3F70A76ED395` (`user_id`),
    PRIMARY KEY (`entrainement_id`, `user_id`),
    CONSTRAINT `FK_EB3D3F70A15E8FD`
        FOREIGN KEY (`entrainement_id`) REFERENCES `entrainement` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `FK_EB3D3F70A76ED395`
        FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS `match_lineup` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `buts` INT DEFAULT NULL,
    `cartons_jaunes` INT DEFAULT NULL,
    `cartons_rouges` INT DEFAULT NULL,
    `position_x` DOUBLE PRECISION DEFAULT NULL,
    `position_y` DOUBLE PRECISION DEFAULT NULL,
    `matchs_id` INT NOT NULL,
    `joueur_id` INT NOT NULL,
    INDEX `IDX_6C11F7CB88EB7468` (`matchs_id`),
    INDEX `IDX_6C11F7CBA9E2D76C` (`joueur_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_6C11F7CB88EB7468`
        FOREIGN KEY (`matchs_id`) REFERENCES `matchs` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `FK_6C11F7CBA9E2D76C`
        FOREIGN KEY (`joueur_id`) REFERENCES `joueur` (`id`)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `order_item` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `quantity` INT NOT NULL,
    `unit_price` NUMERIC(10, 2) NOT NULL,
    `product_id` INT NOT NULL,
    `order_ref_id` INT NOT NULL,
    INDEX `IDX_52EA1F094584665A` (`product_id`),
    INDEX `IDX_52EA1F09E238517C` (`order_ref_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_52EA1F094584665A`
        FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
    CONSTRAINT `FK_52EA1F09E238517C`
        FOREIGN KEY (`order_ref_id`) REFERENCES `order` (`id`)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `message` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `content` LONGTEXT NOT NULL,
    `sent_at` DATETIME NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    INDEX `IDX_B6BD307FF624B39D` (`sender_id`),
    INDEX `IDX_B6BD307FCD53EDB6` (`receiver_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_B6BD307FF624B39D`
        FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `FK_B6BD307FCD53EDB6`
        FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `chat_message` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `message` LONGTEXT NOT NULL,
    `created_at` DATETIME NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `notification_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `auteur_id` INT NOT NULL,
    `destinataire_id` INT NOT NULL,
    `annonce_id` INT NOT NULL,
    INDEX `IDX_FAB3FC1660BB6FE6` (`auteur_id`),
    INDEX `IDX_FAB3FC16A4F84F6E` (`destinataire_id`),
    INDEX `IDX_FAB3FC168805AB2F` (`annonce_id`),
    PRIMARY KEY (`id`),
    CONSTRAINT `FK_FAB3FC1660BB6FE6`
        FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `FK_FAB3FC16A4F84F6E`
        FOREIGN KEY (`destinataire_id`) REFERENCES `user` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `FK_FAB3FC168805AB2F`
        FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `messenger_messages` (
    `id` BIGINT AUTO_INCREMENT NOT NULL,
    `body` LONGTEXT NOT NULL,
    `headers` LONGTEXT NOT NULL,
    `queue_name` VARCHAR(190) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `available_at` DATETIME NOT NULL,
    `delivered_at` DATETIME DEFAULT NULL,
    INDEX `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`, `available_at`, `delivered_at`, `id`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
