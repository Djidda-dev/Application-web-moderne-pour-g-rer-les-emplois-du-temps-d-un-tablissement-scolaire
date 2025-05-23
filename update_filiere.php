<?php
require_once 'database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_filiere'])) {
        throw new Exception("Données invalides");
    }

    $id = $_POST['id_filiere'];
    $nom = trim($_POST['nom_filiere']);
    $desc = trim($_POST['description']);

    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("UPDATE filieres SET 
        NOM_FILIERE = ?,
        DESCRIPTION = ?
        WHERE ID_FILIERE = ?");
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