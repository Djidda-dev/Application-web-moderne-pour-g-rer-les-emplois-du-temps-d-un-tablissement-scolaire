<?php
require_once 'database.php';
header('Content-Type: text/plain');

try {
    // Vérification des champs requis
    if (empty($_POST['nom_salle']) || empty($_POST['description'])) {
        throw new Exception("Tous les champs doivent être remplis.");
    }

    $nom_salle = trim($_POST['nom_salle']);
    $description = trim($_POST['description']);

    // Vérification si la salle existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM salles WHERE NOM_SALLE = ?");
    $stmt->execute([$nom_salle]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cette salle existe déjà.");
    }

    // Insertion dans la base
    $insert = $pdo->prepare("INSERT INTO salles (NOM_SALLE, DESCRIPTION) VALUES (?, ?)");
    $insert->execute([$nom_salle, $description]);

    // Récupérer l'ID de la nouvelle salle
    $new_id = $pdo->lastInsertId();

    // Retourner le nouvel élément HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         htmlspecialchars($nom_salle) . 
         '<span class="badge bg-primary rounded-pill">ID: ' . $new_id . '</span></li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
