<?php
require_once 'database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
        empty($_POST['num_inscription']) || 
        empty($_POST['nom_et']) || 
        empty($_POST['prenom_et']) || 
        empty($_POST['id_filiere']) || 
        empty($_POST['adresse'])) {
        throw new Exception("Données manquantes");
    }

    $num = trim($_POST['num_inscription']);
    $nom = trim($_POST['nom_et']);
    $prenom = trim($_POST['prenom_et']);
    $filiere = trim($_POST['id_filiere']);
    $adresse = trim($_POST['adresse']);

    // Vérification existence étudiant
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NUM_INSCRIPTION = ?");
    $stmt->execute([$num]);
    if ($stmt->fetchColumn() === 0) {
        throw new Exception("Étudiant introuvable");
    }

    // Mise à jour
    $stmt = $pdo->prepare("UPDATE etudiants SET 
        NOM_ET = ?, 
        PRENOM_ET = ?, 
        ID_FILIERE = ?, 
        ADRESSE = ? 
        WHERE NUM_INSCRIPTION = ?");
    
    $stmt->execute([$nom, $prenom, $filiere, $adresse, $num]);
    
    echo "Mise à jour réussie";
    
} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}