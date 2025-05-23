
<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['id_classe']) && !empty($_POST['niveau'])) {
    $id_classe = $_POST['id_classe'];
    $niveau = trim($_POST['niveau']);

    // Vérification avant mise à jour
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE ID_CLASSE = ?");
    $stmt->execute([$id_classe]);
    if ($stmt->fetchColumn() == 0) {
        die("Classe introuvable.");
    }

    // Mise à jour de la classe
    $stmt = $pdo->prepare("UPDATE classes SET NIVEAU = ? WHERE ID_CLASSE = ?");
    $stmt->execute([$niveau, $id_classe]);

    echo "Classe mise à jour avec succès.";
} else {
    http_response_code(400);
    echo "Erreur : Données manquantes.";
}
?>
