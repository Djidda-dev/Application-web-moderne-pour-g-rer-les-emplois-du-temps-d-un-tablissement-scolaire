<?php
require_once 'database.php';

try {
    if (empty($_POST['num_inscription']) || empty($_POST['id_filiere']) || empty($_POST['nom_et']) || empty($_POST['prenom_et']) || empty($_POST['adresse'])) {
        throw new Exception("Tous les champs doivent être remplis.");
    }

    $num_inscription = trim($_POST['num_inscription']);
    $id_filiere = trim($_POST['id_filiere']);
    $nom_et = trim($_POST['nom_et']);
    $prenom_et = trim($_POST['prenom_et']);
    $adresse = trim($_POST['adresse']);

    // Vérification si l'étudiant existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NUM_INSCRIPTION = ?");
    $stmt->execute([$num_inscription]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cet étudiant avec ce numéro d'inscription existe déjà.");
    }

    // Insertion dans la base
    $insert = $pdo->prepare("INSERT INTO etudiants (NUM_INSCRIPTION, ID_FILIERE, NOM_ET, PRENOM_ET, ADRESSE) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$num_inscription, $id_filiere, $nom_et, $prenom_et, $adresse]);

    // Retourner l'étudiant avec les boutons Modifier et Supprimer
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         '<div>' . htmlspecialchars($nom_et) . ' ' . htmlspecialchars($prenom_et) . 
         '<div class="text-muted small">' . htmlspecialchars($adresse) . '</div></div>' .
         '<div class="text-end">' .
         '<span class="badge bg-primary rounded-pill me-2">Inscription: ' . htmlspecialchars($num_inscription) . '</span>' .
         '<button class="btn btn-warning btn-sm btn-edit" data-id="' . htmlspecialchars($num_inscription) . '" data-nom="' . htmlspecialchars($nom_et) . '" data-prenom="' . htmlspecialchars($prenom_et) . '" data-adresse="' . htmlspecialchars($adresse) . '" data-filiere="' . htmlspecialchars($id_filiere) . '">Modifier</button>' .
         '<button class="btn btn-danger btn-sm btn-delete" data-id="' . htmlspecialchars($num_inscription) . '">Supprimer</button>' .
         '</div></li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
