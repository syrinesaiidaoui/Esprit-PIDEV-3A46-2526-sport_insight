<?php
// Standalone DB fix script
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=sport_insight";
$user = "root";
$pass = "";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully to sport_insight database.\n";

    // Adding statut_paiement
    try {
        $pdo->exec("ALTER TABLE contrat_sponsor ADD statut_paiement VARCHAR(50) DEFAULT 'Non payé' NOT NULL");
        echo "SUCCÈS: Colonne 'statut_paiement' ajoutée à 'contrat_sponsor'.\n";
    } catch (Exception $e) {
        echo "INFO: La colonne 'statut_paiement' existe peut-être déjà ou une erreur est survenue: " . $e->getMessage() . "\n";
    }

    // Adding notified (it was also in the entity)
    try {
        $pdo->exec("ALTER TABLE contrat_sponsor ADD notified TINYINT(1) DEFAULT 0 NOT NULL");
        echo "SUCCÈS: Colonne 'notified' ajoutée à 'contrat_sponsor'.\n";
    } catch (Exception $e) {
        echo "INFO: La colonne 'notified' existe peut-être déjà.\n";
    }

    // Adding logo_name to sponsor
    try {
        $pdo->exec("ALTER TABLE sponsor ADD logo_name VARCHAR(255) DEFAULT NULL");
        echo "SUCCÈS: Colonne 'logo_name' ajoutée à 'sponsor'.\n";
    } catch (Exception $e) {
        echo "INFO: La colonne 'logo_name' existe peut-être déjà.\n";
    }

    // Adding updated_at to sponsor
    try {
        $pdo->exec("ALTER TABLE sponsor ADD updated_at DATETIME DEFAULT NULL");
        echo "SUCCÈS: Colonne 'updated_at' ajoutée à 'sponsor'.\n";
    } catch (Exception $e) {
        echo "INFO: La colonne 'updated_at' existe peut-être déjà.\n";
    }

    // Adding adresse to sponsor
    try {
        $pdo->exec("ALTER TABLE sponsor ADD adresse VARCHAR(255) DEFAULT NULL");
        echo "SUCCÈS: Colonne 'adresse' ajoutée à 'sponsor'.\n";
    } catch (Exception $e) {
        echo "INFO: La colonne 'adresse' existe peut-être déjà.\n";
    }

} catch (PDOException $e) {
    echo "ERREUR FATALE: " . $e->getMessage() . "\n";
}
