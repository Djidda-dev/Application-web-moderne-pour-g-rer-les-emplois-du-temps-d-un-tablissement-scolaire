<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['id_classe'])) {
    $id_classe = $_POST['id_classe'];

    // Supprimer la classe
    $stmt = $pdo->prepare("DELETE FROM classes WHERE ID_CLASSE = ?");
    $stmt->execute([$id_classe]);

    // Redirection après suppression
    header("Location: add_classe.php");
    exit;
} else {
    echo "Erreur : ID Classe manquant.";
}
?>
