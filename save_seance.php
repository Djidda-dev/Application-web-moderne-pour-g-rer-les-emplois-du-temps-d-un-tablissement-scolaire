<?php
require_once 'database.php';
header('Content-Type: text/plain');

try {
    // Vérification des champs requis
    foreach (['classe', 'module', 'prof', 'salle', 'jour', 'heure_debut', 'heure_fin'] as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis.");
        }
    }

    // Vérification des formats
    if (!preg_match('/^\d{2}:\d{2}$/', $_POST['heure_debut']) || !preg_match('/^\d{2}:\d{2}$/', $_POST['heure_fin'])) {
        throw new Exception("Format d'heure invalide.");
    }
    if (strtotime($_POST['heure_debut']) >= strtotime($_POST['heure_fin'])) {
        throw new Exception("L'heure de début doit être avant l'heure de fin.");
    }

    // Vérification de l'horaire
    $debut = DateTime::createFromFormat('H:i', $_POST['heure_debut']);
    $fin = DateTime::createFromFormat('H:i', $_POST['heure_fin']);
    if (!$debut || !$fin || $debut >= $fin) {
        throw new Exception("Les heures sont invalides.");
    }

    // Vérification des conflits
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cours WHERE id_salle = ? AND jour = ? 
                           AND (heure_debut < ? AND heure_fin > ?)");
    $stmt->execute([$_POST['salle'], $_POST['jour'], $_POST['heure_fin'], $_POST['heure_debut']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Conflit d'horaire pour cette salle !");
    }

    // Vérification des conflits pour le professeur
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cours 
        WHERE id_prof = ? 
          AND jour = ? 
          AND (
                (heure_debut < ? AND heure_fin > ?) -- chevauchement
             )");
    $stmt->execute([
        $_POST['prof'],
        $_POST['jour'],
        $_POST['heure_fin'],
        $_POST['heure_debut']
    ]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce professeur a déjà une séance à cet horaire !");
    }

    // Calcul de la durée de la séance à ajouter (en heures)
    $duree = (strtotime($_POST['heure_fin']) - strtotime($_POST['heure_debut'])) / 3600;

    // Récupérer le volume horaire déjà programmé pour ce module et cette classe
    $stmt = $pdo->prepare("SELECT SUM(TIMESTAMPDIFF(MINUTE, heure_debut, heure_fin))/60 AS heures 
                       FROM cours 
                       WHERE id_module = ? AND id_classe = ?");
    $stmt->execute([$_POST['module'], $_POST['classe']]);
    $heuresDejaProgrammees = $stmt->fetchColumn();
    $heuresDejaProgrammees = $heuresDejaProgrammees ?: 0;

    // Récupérer le volume horaire total du module
    $stmt = $pdo->prepare("SELECT volume_horaire FROM modules WHERE ID_MODULE = ?");
    $stmt->execute([$_POST['module']]);
    $volumeTotal = $stmt->fetchColumn();

    if (($heuresDejaProgrammees + $duree) > $volumeTotal) {
        throw new Exception("Impossible : le volume horaire total du module serait dépassé !");
    }

    // Insertion dans la base
    $stmt = $pdo->prepare("INSERT INTO cours (id_classe, id_module, id_prof, id_salle, jour, heure_debut, heure_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['classe'],
        $_POST['module'],
        $_POST['prof'],
        $_POST['salle'],
        $_POST['jour'],
        $_POST['heure_debut'],
        $_POST['heure_fin']
    ]);

    echo "Séance ajoutée avec succès !";
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
