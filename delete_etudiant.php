<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['num_inscription'])) {
    try {
        $num = $_POST['num_inscription'];
        
        $stmt = $pdo->prepare("DELETE FROM etudiants WHERE NUM_INSCRIPTION = ?");
        $stmt->execute([$num]);
        
        header("Location: add_etudiant.php");
        exit;
        
    } catch (PDOException $e) {
        die("Erreur de suppression : " . $e->getMessage());
    }
} else {
    die("Requête invalide");
}