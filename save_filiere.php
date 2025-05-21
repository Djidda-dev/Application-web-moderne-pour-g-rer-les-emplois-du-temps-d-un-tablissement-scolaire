<?php
require_once 'database.php';
header('Content-Type: text/plain');

try {
    // Vérification des champs requis
    if (empty($_POST['nom_filiere']) || empty($_POST['description'])) {
        throw new Exception("Tous les champs doivent être remplis.");
    }

    $nom_filiere = trim($_POST['nom_filiere']);
    $description = trim($_POST['description']);

    // Vérification si la filière existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM filieres WHERE NOM_FILIERE = ?");
    $stmt->execute([$nom_filiere]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cette filière existe déjà.");
    }

    // Insertion dans la base
    $insert = $pdo->prepare("INSERT INTO filieres (NOM_FILIERE, DESCRIPTION) VALUES (?, ?)");
    $insert->execute([$nom_filiere, $description]);

    // Récupérer l'ID de la nouvelle filière
    $new_id = $pdo->lastInsertId();

    // Retourner le nouvel élément HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         htmlspecialchars($nom_filiere) . 
         '<span class="badge bg-primary rounded-pill">ID: ' . $new_id . '</span></li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
