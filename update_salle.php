<?php
require_once 'database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_salle'])) {
        throw new Exception("Données invalides");
    }

    $id = $_POST['id_salle'];
    $nom = trim($_POST['nom_salle']);
    $desc = trim($_POST['description']);

    $pdo->beginTransaction();
    
    // Vérification conflit de nom
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM salles WHERE NOM_SALLE = ? AND ID_SALLE != ?");
    $stmt->execute([$nom, $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce nom de salle est déjà utilisé");
    }

    $stmt = $pdo->prepare("UPDATE salles SET 
        NOM_SALLE = ?,
        DESCRIPTION = ?
        WHERE ID_SALLE = ?");
    $stmt->execute([$nom, $desc, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Aucune modification effectuée");
    }
    
    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}