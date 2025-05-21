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

    // Insertion dans la base
    $insert = $pdo->prepare("INSERT INTO cours (id_classe, id_module, id_prof, id_salle, jour, heure_debut, heure_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([
        $_POST['classe'], $_POST['module'], $_POST['prof'], $_POST['salle'], $_POST['jour'],
        $debut->format('H:i:s'), $fin->format('H:i:s')
    ]);

    echo "Séance ajoutée avec succès !";
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
