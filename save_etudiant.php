<?php
require_once 'database.php';
header('Content-Type: text/plain');

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

    // Retourner le nouvel élément HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
         htmlspecialchars($nom_et) . ' ' . htmlspecialchars($prenom_et) . 
         '<span class="badge bg-primary rounded-pill">Inscription: ' . htmlspecialchars($num_inscription) . '</span></li>';
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}
