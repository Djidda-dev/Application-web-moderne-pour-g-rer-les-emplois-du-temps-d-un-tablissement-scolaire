<?php
require_once 'database.php';
header('Content-Type: text/plain');

try {
    if (empty($_POST['id_module']) || empty($_POST['nom_module']) || empty($_POST['description'])) {
        throw new Exception("Tous les champs doivent être remplis.");
    }

    $id_module = trim($_POST['id_module']); // ID_MODULE vient du formulaire
    $nom_module = trim($_POST['nom_module']);
    $description = trim($_POST['description']);

    // Vérification si l'ID_MODULE existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE ID_MODULE = ?");
    $stmt->execute([$id_module]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cet ID de module existe déjà.");
    }

    // Insertion dans la base avec l'ID fourni
    $insert = $pdo->prepare("INSERT INTO modules (ID_MODULE, NOM_MODULE, DESCRIPTION) VALUES (?, ?, ?)");
    $insert->execute([$id_module, $nom_module, $description]);

    // Retourner le nouvel élément HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         htmlspecialchars($nom_module) . 
         '<span class="badge bg-primary rounded-pill">ID: ' . htmlspecialchars($id_module) . '</span></li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
