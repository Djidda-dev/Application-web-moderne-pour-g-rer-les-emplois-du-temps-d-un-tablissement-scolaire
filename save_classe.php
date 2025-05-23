<?php 
require_once 'database.php'; 

try {
    if (empty($_POST['niveau']) || empty($_POST['id_filiere'])) {
        throw new Exception("Tous les champs doivent être remplis.");
    }

    $niveau = trim($_POST['niveau']);
    $id_filiere = trim($_POST['id_filiere']);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE NIVEAU = ? AND ID_FILIERE = ?");
    $stmt->execute([$niveau, $id_filiere]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cette classe avec cette filière existe déjà.");
    }

    $insert = $pdo->prepare("INSERT INTO classes (NIVEAU, ID_FILIERE) VALUES (?, ?)");
    $insert->execute([$niveau, $id_filiere]);

    // Récupérer l'ID généré et renvoyer la nouvelle classe sous forme HTML
    $new_id = $pdo->lastInsertId();
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         htmlspecialchars($niveau) . 
         '<span class="badge bg-primary rounded-pill">ID: ' . $new_id . '</span>' .
         '<div>
            <button class="btn btn-warning btn-sm btn-edit" data-id="' . $new_id . '" data-niveau="' . htmlspecialchars($niveau) . '" data-bs-toggle="modal" data-bs-target="#editModal">Modifier</button>
            <form action="delete_classe.php" method="POST" class="d-inline" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette classe ?\')">
                <input type="hidden" name="id_classe" value="' . $new_id . '">
                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
            </form>
        </div>' .
         '</li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
