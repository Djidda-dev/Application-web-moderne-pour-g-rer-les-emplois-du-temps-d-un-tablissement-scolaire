<?php
require_once 'database.php';
header('Content-Type: text/plain');

try {
    // Vérification des champs requis
    if (empty($_POST['nom_prof']) || empty($_POST['tel'])) {
        throw new Exception("Tous les champs doivent être remplis.");
    }

    $nom_prof = trim($_POST['nom_prof']);
    $tel = trim($_POST['tel']);

    // Vérification si le numéro de téléphone est unique
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM professeurs WHERE TEL = ?");
    $stmt->execute([$tel]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Un professeur avec ce numéro existe déjà.");
    }

    // Insertion du professeur
    $insert = $pdo->prepare("INSERT INTO professeurs (NOM_PROF, TEL) VALUES (?, ?)");
    $insert->execute([$nom_prof, $tel]);

    // Récupérer l'ID du nouveau professeur
    $new_id = $pdo->lastInsertId();

    // Retourner le nouvel élément HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         htmlspecialchars($nom_prof) . 
         '<span class="badge bg-primary rounded-pill">Téléphone: ' . htmlspecialchars($tel) . '</span></li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
